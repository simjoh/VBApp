import {Injectable} from '@angular/core';
import {map, mergeMap, switchMap, tap, catchError} from "rxjs/operators";
import {RusaTimeCalculationApiService} from "./rusa-time-calculation-api.service";
import {BehaviorSubject, combineLatest, Observable, of, throwError} from "rxjs";

import {EventService} from "../../shared/service/event.service";
import {EventRepresentation} from "../../../shared/api/api";
import { HttpClient } from "@angular/common/http";
import {
  RusaPlannerControlInputRepresentation,
  RusaPlannerInputRepresentation,
  RusaPlannerResponseRepresentation
} from "../../../shared/api/rusaTimeApi";
import { TrackService } from 'src/app/shared/track-service';
import { EventTrackInfo } from './track-builder-track-info-form/track-builder-track-info-form.component';

@Injectable()
export class TrackBuilderComponentService {
  private currentTrackId: string | null = null;
  private trackSavedSubject = new BehaviorSubject<boolean>(false);
  trackSaved$ = this.trackSavedSubject.asObservable();

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
    })
  );

  $rusaPlannerInputSubject = new BehaviorSubject<RusaPlannerInputRepresentation>({} as RusaPlannerInputRepresentation);
  $rusaPlannerInput = this.$rusaPlannerInputSubject.asObservable();

  $rusaPlannerControlsSubject = new BehaviorSubject<RusaPlannerControlInputRepresentation[]>([]);
  $rusaPlannerControlsInput = this.$rusaPlannerControlsSubject.asObservable();

  $summarySubject = new BehaviorSubject<RusaPlannerResponseRepresentation | null>(null);
  $summary = this.$summarySubject.asObservable();

  $all = combineLatest([
    this.$currentEvent,
    this.$rusaPlannerInput,
    this.$rusaPlannerControlsInput
  ]).pipe(
    map(([event, rusaplanner, controls]) => {
      const updatedPlanner = {
        ...rusaplanner,
        controls: [...controls],
        event_uid: event.event_uid
      };
      return updatedPlanner;
    }),
    switchMap((input) => {
      if (input.event_uid && (input.event_distance || input.controls.length > 0)) {
        return this.rusatimeService.addSite(input);
      }
      return of(null);
    }),
    tap((response: RusaPlannerResponseRepresentation | null) => {
      if (response) {
        this.$summarySubject.next(response);
      }
    })
  );

  constructor(
    private rusatimeService: RusaTimeCalculationApiService,
    private trackService: TrackService,
    private eventService: EventService,
    private httpClient: HttpClient
  ) {
    this.$all.subscribe();
  }

  choosenEvent(eventUid: string) {
    this.$choosenEventSubject.next(eventUid);
  }

  rusaInput(input: RusaPlannerInputRepresentation) {
    this.$rusaPlannerInputSubject.next(input);
  }

  addControls(controls: RusaPlannerControlInputRepresentation[]) {
    this.$rusaPlannerControlsSubject.next(controls);
  }

  createTrack() {
    const currentSummary = this.$summarySubject.getValue();
    if (currentSummary) {
      this.trackService.createTrack(currentSummary);
    }
  }

  saveTrack(track: EventTrackInfo): Observable<any> {
    const trackData = {
      event_distance: track.trackdistance,
      start_time: track.starttime || '07:00',
      start_date: track.startdate || new Date().toISOString().split('T')[0],
      event_uid: track.event_uid,
      track_title: track.trackname,
      controls: [],
      link: track.link
    };

    // Get the current summary
    const summary = this.$summarySubject.getValue();
    if (!summary) {
      return throwError(() => new Error('No track data available'));
    }

    // If we have a currentTrackId, update the track
    if (this.currentTrackId) {
      return this.trackService.updateTrack(summary, this.currentTrackId).pipe(
        tap(response => {
          this.trackSavedSubject.next(true);
          this.rusaInput(trackData);
        }),
        catchError(error => {
          console.error('Failed to update track:', error);
          this.trackSavedSubject.next(false);
          return throwError(() => error);
        })
      );
    }

    // Otherwise create a new track
    return this.trackService.createTrack(summary).pipe(
      tap(response => {
        this.currentTrackId = response.track_uid;
        this.trackSavedSubject.next(true);
        this.rusaInput(trackData);
      }),
      catchError(error => {
        console.error('Failed to save track:', error);
        this.trackSavedSubject.next(false);
        return throwError(() => error);
      })
    );
  }

  getCurrentTrackId(): string | null {
    return this.currentTrackId;
  }
}
