import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {Link, TrackRepresentation} from "./api/api";
import {environment} from "../../environments/environment";
import {catchError, map, mergeMap, shareReplay, take, tap} from "rxjs/operators";
import {BehaviorSubject, Observable, ReplaySubject, throwError} from "rxjs";
import {HttpMethod} from "../core/HttpMethod";

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

  public updatetrack(track: TrackRepresentation){


  }

  public deletelinkExists(track: TrackRepresentation): boolean {
     return this.linkService.exists(track.links,'relation.track.delete' , 'DELETE');
  }

  public currentTrack(trackUid: string){
    this.$currentTrackSubject.next(trackUid);
  }
}
