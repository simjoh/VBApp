import { Injectable } from '@angular/core';
import {CompetitorService} from "../competitor.service";
import {AuthService} from "../../core/auth/auth.service";
import {combineLatest, map, mergeMap, tap} from "rxjs/operators";
import {RandonneurCheckPointRepresentation} from "../../shared/api/api";
import {BehaviorSubject} from "rxjs";
import {TrackService} from "../track.service";

@Injectable()
export class CompetitorListComponentService {

  stampEnableSubject = new BehaviorSubject<boolean>(true);
  stampEnable$ = this.stampEnableSubject.asObservable();

  dnfEnableSubject = new BehaviorSubject<boolean>(true);
  dnfEnable$ = this.dnfEnableSubject.asObservable()

  rollbackEnableSubject = new BehaviorSubject<boolean>(true);
  rollbackEnable$ = this.rollbackEnableSubject.asObservable();

  $controls =  this.authService.$auth$.pipe(
    mergeMap((auth) => {
      return this.competitorService.getCheckpoints(auth.startnumber, auth.trackuid, auth.id)
    }),
    map((test: Array<RandonneurCheckPointRepresentation>) => {
      return test;
    })
  );

  $track =  this.authService.$auth$.pipe(
    mergeMap((auth) => {
      return this.trackService.getTrack(auth.trackuid)
    }),
    map((test: any) => {
      return test;
    })
  );






  constructor(private competitorService: CompetitorService, private authService: AuthService, private trackService: TrackService) {


  }
}
