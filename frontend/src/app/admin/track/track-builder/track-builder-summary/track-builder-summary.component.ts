import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import {TrackBuilderComponentService} from "../track-builder-component.service";
import {map, mergeMap, switchMap, tap} from "rxjs/operators";
import {combineLatest, Observable, of} from "rxjs";

@Component({
  selector: 'brevet-track-builder-summary',
  templateUrl: './track-builder-summary.component.html',
  styleUrls: ['./track-builder-summary.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackBuilderSummaryComponent implements OnInit {

  event$ = this.trackbuildercomponentService.$currentEvent.pipe(
    map(event => {
      return event;
    }),
    tap(() => this.cdr.markForCheck())
  );

  $track = this.trackbuildercomponentService.$all.pipe(
    map((payload:any) => {
      if (payload && payload.rusaTrackRepresentation){
        return payload.rusaTrackRepresentation;
      } else {
        return null;
      }
    }),
    tap(() => this.cdr.markForCheck())
  );

  $controls = this.trackbuildercomponentService.$all.pipe(
    map((payload:any) => {
      if (payload && payload.rusaplannercontrols){
        return payload.rusaplannercontrols;
      } else {
        return null;
      }
    }),
    tap(() => this.cdr.markForCheck())
  );

  $buttonDisable = combineLatest([this.$track, this.$controls]).pipe(
    map(([track, controls]) => {
      if (track && controls && controls.length > 0){
        return controls.some(e => {
          return e.rusaControlRepresentation &&
                 e.rusaControlRepresentation.CONTROL_DISTANCE_KM >= track.EVENT_DISTANCE_KM;
        });
      } else {
        return false;
      }
    }),
    tap(() => this.cdr.markForCheck())
  );

  constructor(
    private trackbuildercomponentService: TrackBuilderComponentService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
    this.cdr.detectChanges();
  }

  createTrack() {
    this.trackbuildercomponentService.createTrack();
  }
}
