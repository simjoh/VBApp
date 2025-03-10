import {Injectable} from '@angular/core';
import {map, mergeMap, switchMap, take, tap} from "rxjs/operators";
import {RusaTimeCalculationApiService} from "./rusa-time-calculation-api.service";
import {BehaviorSubject, combineLatest, of} from "rxjs";
import {MessageService} from "primeng/api";

import {EventService} from "../../shared/service/event.service";
import {EventRepresentation} from "../../../shared/api/api";
import { HttpClient } from "@angular/common/http";
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
      if (!rusaplanner || !event || !event.event_uid) {
        return null;
      }

      const inputData = { ...rusaplanner };
      inputData.controls = [...controls];
      inputData.event_uid = event.event_uid;

      return inputData;
    }),
    switchMap((input) => {
      if (!input) {
        return of(null);
      }

      input.use_acp_calculator = true;

      return this.rusatimeService.addSite(input);
    }),
    tap((rusa) => {
      console.log('RUSA/ACP response:', rusa);
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

  constructor(private rusatimeService: RusaTimeCalculationApiService, private trackService: TrackService, private eventService: EventService,  private httpClient: HttpClient, private messageService: MessageService) {
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

  async createTrack() {
    try {
      const trackData = await this.$all.pipe(take(1)).toPromise();
      if (!trackData) {
        this.messageService.add({
          key: 'tc',
          severity: 'error',
          summary: 'Fel',
          detail: 'Ingen bandata att spara.'
        });
        return;
      }

      await this.trackService.createTrack(trackData as RusaPlannerResponseRepresentation);

      this.messageService.add({
        key: 'tc',
        severity: 'success',
        summary: 'Bana sparad',
        detail: 'Banan har sparats framgångsrikt!'
      });
    } catch (error) {
      console.error('Error creating track:', error);
      this.messageService.add({
        key: 'tc',
        severity: 'error',
        summary: 'Sparande misslyckades',
        detail: 'Det gick inte att spara banan. Försök igen senare.'
      });
    }
  }
}
