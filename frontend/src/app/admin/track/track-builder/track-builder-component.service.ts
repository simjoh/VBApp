import { Injectable } from '@angular/core';
import {map, mergeMap} from "rxjs/operators";
import {RusaTimeCalculationApiService} from "./rusa-time-calculation-api.service";
import {BehaviorSubject} from "rxjs";
import {TrackService} from "../../shared/service/track.service";

@Injectable()
export class TrackBuilderComponentService {


  $choosenEventSubject = new BehaviorSubject<string>(null);
  $choosenEventUid =this.$choosenEventSubject.asObservable();


  $current = new BehaviorSubject<boolean>(false);

  aktuell = this.$current.asObservable().pipe(
    map((s) => {
      return this.rusatimeService.addSite(null);
    }),
    mergeMap((dd) => {
      return dd
    })
  )


  constructor(private rusatimeService: RusaTimeCalculationApiService, private trackService: TrackService) { }



  read(){
    this.$current.next(true)
  }


  choosenEvent(eventUid: string){
    this.$choosenEventSubject.next(eventUid);
  }

}
