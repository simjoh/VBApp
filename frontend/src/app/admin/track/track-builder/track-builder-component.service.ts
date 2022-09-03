import {Injectable} from '@angular/core';
import {map, mergeMap, switchMap, tap} from "rxjs/operators";
import {RusaTimeCalculationApiService} from "./rusa-time-calculation-api.service";
import {BehaviorSubject, combineLatest, of} from "rxjs";

import {EventService} from "../../shared/service/event.service";
import {EventRepresentation} from "../../../shared/api/api";
import {HttpClient} from "@angular/common/http";
import {
  RusaPlannerControlInputRepresentation,
  RusaPlannerInputRepresentation, RusaPlannerResponseRepresentation,
  RusaTimeRepresentation
} from "../../../shared/api/rusaTimeApi";
import { TrackService } from 'src/app/shared/track-service';

@Injectable()
export class TrackBuilderComponentService {

  $choosenEventSubject = new BehaviorSubject<string>("0");
  $choosenEventUid = this.$choosenEventSubject.asObservable();

  $currentEvent = this.$choosenEventUid.pipe(
    switchMap((term) => {
      if (term != "0") {
        return this.eventService.getEvent(term)
      } else {
        return of({} as EventRepresentation);
      }
    }),
    map((res1: EventRepresentation) => {
      if (res1) {
        return res1;
      } else {
        return {} as EventRepresentation;
      }
    }),
    map((res: EventRepresentation) => {
      return res;
    })
  )

  $rusaPlannerInputSubject = new BehaviorSubject<RusaPlannerInputRepresentation>({} as RusaPlannerInputRepresentation)
  $rusaPlannerInput = this.$rusaPlannerInputSubject.asObservable()

  $test = this.$rusaPlannerInput.pipe(
    map((input1) => {
      if (input1.event_distance){
        return input1;
      } else {
        return null;
      }
    }),
    mergeMap((input) => {
      if (input){
        return this.rusatimeService.addSite(input);
      } else {
        return of(null);
      }
    }),
    map((response) => {
      return response;
    })
  );


  $rusaPlannerControlsSubject = new BehaviorSubject<RusaPlannerControlInputRepresentation[]>([])
  $rusaPlannerControlsInput = this.$rusaPlannerControlsSubject.asObservable()


  $summarySubject = new BehaviorSubject<RusaTimeRepresentation>(null);


  $all = combineLatest([this.$currentEvent, this.$rusaPlannerInput, this.$rusaPlannerControlsInput]).pipe(
    map(([event, rusaplanner, controls]) => {
      rusaplanner.controls = controls;
      rusaplanner.event_uid = event.event_uid;

      return rusaplanner
    }),
    switchMap((input) => {
      return this.rusatimeService.addSite(input);
    }),
    tap((rusa) => {
       console.log(rusa);
    })
  );


  $current = new BehaviorSubject<boolean>(false);
  aktuell = this.$current.asObservable().pipe(
    map((s) => {
      return this.rusatimeService.addSite(null);
    }),
    mergeMap((dd) => {
      return dd
    })
  )

  constructor(private rusatimeService: RusaTimeCalculationApiService, private trackService: TrackService, private eventService: EventService,  private httpClient: HttpClient) {
  }


  read() {
    this.$current.next(true)
  }

  choosenEvent(eventUid: string) {
    this.$choosenEventSubject.next(eventUid);
  }

  rusaInput(rusainput: RusaPlannerInputRepresentation){
    this.$rusaPlannerInputSubject.next(rusainput);
  }

  addControls(rusatimeControls: Array<RusaPlannerControlInputRepresentation>) {
    this.$rusaPlannerControlsSubject.next(rusatimeControls);
  }

  createTrack() {
    this.$all.pipe(
      map((s:RusaPlannerResponseRepresentation) => {
        this.trackService.createTrack(s)
      })
    ).toPromise()
  }
}
