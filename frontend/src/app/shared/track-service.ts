import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {Link, TrackRepresentation} from "./api/api";
import {environment} from "../../environments/environment";
import {catchError, map, shareReplay, take, tap} from "rxjs/operators";
import {BehaviorSubject, firstValueFrom, Observable, throwError} from "rxjs";
import {HttpMethod} from "../core/HttpMethod";
import {RusaPlannerResponseRepresentation, RusaTimeRepresentation} from './api/rusaTimeApi';

@Injectable({
  providedIn: 'root'
})
export class TrackService {


  $currentTrackSubject = new BehaviorSubject<string | null>(null);
  $currentTrack = this.$currentTrackSubject.asObservable().pipe(
    map((val) => {
      return val;
    })
  );

  // Add a new subject for track representation
  private currentTrackRepresentationSubject = new BehaviorSubject<TrackRepresentation | null>(null);
  currentTrackRepresentation$ = this.currentTrackRepresentationSubject.asObservable();

  constructor(private httpClient: HttpClient, private linkService: LinkService) { }

  private normalizeTrackData(track: TrackRepresentation): TrackRepresentation {
    if (track) {
      // Convert active to number (1 or 0)
      if (typeof track.active === 'boolean') {
        track.active = track.active ? 1 : 0;
      } else if (typeof track.active === 'string') {
        track.active = track.active === 'true' ? 1 : track.active === 'false' ? 0 : Number(track.active);
      } else {
        track.active = Number(track.active);
      }
      // console.log('Normalized track active state:', track.active);
    }
    return track;
  }

  allTracks$ = this.getAllTracks();


  public tracksForEvent(eventuid: string){
    const path = 'tracks/event/' + eventuid;
    return this.httpClient.get<Array<any>>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: Array<any>) => {
        return checkpoints;
      }),
      tap((checkpoints: Array<any>) => {
        console.log(checkpoints);
      }),
      shareReplay(1)
    ) as Observable<Array<any>>;
  }

  public getAllTracks(){
    const path = 'tracks';
    return this.httpClient.get<Array<TrackRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((tracks: Array<TrackRepresentation>) => {
        return tracks.map(track => this.normalizeTrackData(track));
      }),
      tap((tracks: Array<TrackRepresentation>) => {
        console.log('Normalized tracks:', tracks);
      }),
      shareReplay(1)
    ) as Observable<Array<TrackRepresentation>>;
  }


  public deletetrack(track: TrackRepresentation){
    const link = this.linkService.findByRel(track.links, 'relation.track.delete', HttpMethod.DELETE );
    return this.httpClient.delete(link.url)
      .pipe(
        catchError(err => {
          return throwError(err);
        })
      ).toPromise().then((s) => {
       // this.removeSubject.next(eventUid);
      })
  }

  async publishresultaction(link: Link): Promise<TrackRepresentation> {
    if (!link) {
      throw new Error('Link not found - cannot perform publish action');
    }
    console.log('Making PUT request to:', link.url, 'with method:', link.method);

    try {
      // First make the publish/unpublish request
      await this.httpClient.put(link.url, {}, {}).pipe(
        catchError(err => {
          console.error('PUT request failed:', err);
          return throwError(() => err);
        })
      ).toPromise();

      // Extract track_uid from the URL
      const match = link.url.match(/\/track\/([^/?]+)/);
      if (!match) {
        throw new Error('Could not extract track ID from URL');
      }
      const trackUid = match[1];

      // Then fetch the updated track data
      return await this.getTrack(trackUid).pipe(
        take(1),
        map(track => {
          console.log('Track fetched after publish action:', track);
          return track;
        })
      ).toPromise();
    } catch (error) {
      console.error('Error in publish action:', error);
      throw error;
    }
  }

  public deletelinkExists(track: TrackRepresentation): boolean {
     return this.linkService.exists(track.links,'relation.track.delete' , 'DELETE');
  }

  public currentTrack(trackUid: string | null) {
    // Don't make API calls for null/empty track IDs
    if (!trackUid || trackUid === '') {
      this.$currentTrackSubject.next(null);
      this.currentTrackRepresentationSubject.next(null);
      return;
    }

    this.$currentTrackSubject.next(trackUid);
    this.getTrack(trackUid).subscribe({
      next: (track) => {
        this.currentTrackRepresentationSubject.next(track);
      },
      error: (error) => {
        console.error('Error loading track:', error);
        // Reset state on error
        this.$currentTrackSubject.next(null);
        this.currentTrackRepresentationSubject.next(null);
      }
    });
  }

  public getCurrentTrackUid(): string | null {
    const trackUid = this.$currentTrackSubject.getValue();
    return trackUid && trackUid !== '' ? trackUid : null;
  }

  public getCurrentTrackRepresentation(): TrackRepresentation | null {
    return this.currentTrackRepresentationSubject.getValue();
  }

  publishReultLinkExists(track: TrackRepresentation) {
    // Check for publish link (when track is unpublished)
    return this.linkService.exists(track.links, 'relation.track.undopublisresults', HttpMethod.PUT);
  }


  async undopublishresult(trackRepresentation: TrackRepresentation): Promise<TrackRepresentation> {
    const link = this.linkService.findByRel(trackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT);
    console.log('Undopublish link found:', link);
    if (!link) {
      throw new Error('Undopublish link not found - track may already be unpublished');
    }
    const result = await this.publishresultaction(link);
    console.log('Undopublish result:', result);
    return result;
  }

  async publishresult(trackRepresentation: TrackRepresentation): Promise<TrackRepresentation> {
    const link = this.linkService.findByRel(trackRepresentation.links, 'relation.track.undopublisresults', HttpMethod.PUT);
    console.log('Publish link found:', link);
    if (!link) {
      throw new Error('Publish link not found - track may already be published');
    }
    const result = await this.publishresultaction(link);
    console.log('Publish result:', result);
    return result;
  }

  async createTrack(s: RusaPlannerResponseRepresentation): Promise<any> {
      return firstValueFrom(this.httpClient.post(environment.backend_url + "trackplanner/createtrackfromplanner", s))
        .catch(error => {
          console.error('Error creating track:', error);
          throw error; // Re-throw to allow handling by the caller
        });
  }

  async createTrackWithFormData(requestPayload: any): Promise<any> {
      return firstValueFrom(this.httpClient.post(environment.backend_url + "trackplanner/createtrackfromplanner", requestPayload))
        .catch(error => {
          console.error('Error creating track with form data:', error);
          throw error; // Re-throw to allow handling by the caller
        });
  }

  public getTrack(trackUid: string): Observable<TrackRepresentation> {
    if (!trackUid || trackUid === '') {
      return throwError(() => new Error('Invalid track ID'));
    }

    const path = 'track/' + trackUid;
    return this.httpClient.get<TrackRepresentation>(environment.backend_url + path).pipe(
      take(1),
      map((track: TrackRepresentation) => {
        const normalizedTrack = this.normalizeTrackData(track);
        this.currentTrackRepresentationSubject.next(normalizedTrack);
        return normalizedTrack;
      }),
      catchError((error) => {
        console.error('Error fetching track:', error);
        this.currentTrackRepresentationSubject.next(null);
        return throwError(() => error);
      })
    );
  }
}
