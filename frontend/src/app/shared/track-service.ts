import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {Link, TrackRepresentation} from "./api/api";
import {environment} from "../../environments/environment";
import {catchError, map, shareReplay, take, tap} from "rxjs/operators";
import {BehaviorSubject, firstValueFrom, Observable, throwError, of, mergeMap} from "rxjs";
import {HttpMethod} from "../core/HttpMethod";
import {RusaPlannerResponseRepresentation, RusaTimeRepresentation, RusaPlannerInputRepresentation, RusaPlannerControlInputRepresentation} from './api/rusaTimeApi';
import { v4 as uuidv4 } from 'uuid';  // Import UUID generator

export interface SaveControlsRequest {
  track_uid: string;
  controls: RusaPlannerControlInputRepresentation[];
}

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
    return await  this.httpClient.put(link.url,{},  {})
      .pipe(
        catchError(err => {
          return throwError(err);
        })
      ).toPromise().then((res) => {
        return res
      });
  }

  public deletelinkExists(track: TrackRepresentation): boolean {
     return this.linkService.exists(track.links,'relation.track.delete' , 'DELETE');
  }

  public currentTrack(trackUid: string){
    this.$currentTrackSubject.next(trackUid);
  }

  publishReultLinkExists(track: TrackRepresentation) {
    return this.linkService.exists(track.links, 'relation.track.publisresults', HttpMethod.PUT );
  }


  async undopublishresult(trackRepresentation: TrackRepresentation) {
    return await this.publishresultaction(this.linkService.findByRel(trackRepresentation.links, 'relation.track.undopublisresults', HttpMethod.PUT ))
  }

  async publishresult(trackRepresentation: TrackRepresentation) {
    return await this.publishresultaction(this.linkService.findByRel(trackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT ))
  }

  createTrack(trackData: RusaPlannerResponseRepresentation): Observable<any> {
    console.log('Creating track with data:', JSON.stringify(trackData, null, 2));
    return this.httpClient.post(`${environment.backend_url}trackplanner/createtrackfromplanner`, trackData).pipe(
      tap(response => {
        console.log('Track created:', response);
      }),
      catchError(error => {
        console.error('Error creating track:', error);
        return throwError(() => error);
      })
    );
  }

  updateTrack(trackData: RusaPlannerResponseRepresentation, track_uid: string): Observable<any> {
    console.log('Updating track with data:', JSON.stringify(trackData, null, 2));
    return this.httpClient.put(`${environment.backend_url}trackplanner/updatetrackfromplanner/${track_uid}`, trackData).pipe(
      tap(response => {
        console.log('Track updated:', response);
      }),
      catchError(error => {
        console.error('Error updating track:', error);
        return throwError(() => error);
      })
    );
  }
}
