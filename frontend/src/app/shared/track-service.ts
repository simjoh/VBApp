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


  $currentTrackSubject = new BehaviorSubject("");
  $currentTrack = this.$currentTrackSubject.asObservable().pipe(
    map((val) => {
      return val;
    })
  );

  constructor(private httpClient: HttpClient, private linkService: LinkService) { }


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
        return tracks;
      }),
      tap((tracks: Array<TrackRepresentation>) => {
        console.log(tracks);
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

  async  publishresultaction(link: Link): Promise<any>{
    if (!link) {
      throw new Error('Link not found - cannot perform publish action');
    }
    console.log('Making PUT request to:', link.url, 'with method:', link.method);
    return await  this.httpClient.put(link.url,{},  {})
      .pipe(
        catchError(err => {
          console.error('PUT request failed:', err);
          return throwError(err);
        })
      ).toPromise().then((res) => {
        console.log('PUT request successful:', res);
        return res
      });
  }

  public deletelinkExists(track: TrackRepresentation): boolean {
     return this.linkService.exists(track.links,'relation.track.delete' , 'DELETE');
  }

  public currentTrack(trackUid: string){
    this.$currentTrackSubject.next(trackUid);
  }

  public getCurrentTrackUid(): string {
    return this.$currentTrackSubject.getValue();
  }

  publishReultLinkExists(track: TrackRepresentation) {
    return this.linkService.exists(track.links, 'relation.track.publisresults', HttpMethod.PUT );
  }


  async undopublishresult(trackRepresentation: TrackRepresentation) {
    // Try the old format first since we can see it in the links
    const link = this.linkService.findByRel(trackRepresentation.links, 'relation.track.undopublisresults', HttpMethod.PUT);
    console.log('Undopublish link found:', link);
    if (!link) {
      throw new Error('Undopublish link not found - track may already be unpublished');
    }
    const result = await this.publishresultaction(link);
    console.log('Undopublish result:', result);
    return result;
  }

  async publishresult(trackRepresentation: TrackRepresentation) {
    // Try the old format first since we can see it in the links
    const link = this.linkService.findByRel(trackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT);
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
    const path = 'track/' + trackUid;
    return this.httpClient.get<TrackRepresentation>(environment.backend_url + path).pipe(
      take(1),
      map((track: TrackRepresentation) => {
        return track;
      })
    );
  }
}
