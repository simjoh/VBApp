import { Injectable } from '@angular/core';
import {BehaviorSubject} from "rxjs";
import {TrackRepresentation} from "../../shared/api/api";
import {TrackService} from "../../shared/track-service";
import {privateDecrypt} from "crypto";
import {map, mergeMap} from "rxjs/operators";

@Injectable()
export class ParticipantComponentService {


  trackSubject = new BehaviorSubject<string>(null);
  track$ = this.trackSubject.asObservable().pipe();

  tracks$ = this.trackService.getAllTracks().pipe(
    map((tracks) => {
      return tracks;
    })
  );

  constructor(private trackService: TrackService) { }


  public track(trackuid: string){
    this.trackSubject.next(trackuid);
  }
}
