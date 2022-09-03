import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {TrackBuilderComponentService} from "../track-builder-component.service";
import {map, mergeMap, switchMap} from "rxjs/operators";
import {combineLatest, Observable, of} from "rxjs";

@Component({
  selector: 'brevet-track-builder-summary',
  templateUrl: './track-builder-summary.component.html',
  styleUrls: ['./track-builder-summary.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackBuilderSummaryComponent implements OnInit {

  event$ = this.trackbuildercomponentService.$currentEvent.pipe(
    map(switchMap => {
      return switchMap;
    })
  );

  $track = this.trackbuildercomponentService.$all.pipe(
    map((payload:any) => {
      if (payload){
        return payload.rusaTrackRepresentation
      } else {
        return of(null)
      }
    })
  )

  $controls = this.trackbuildercomponentService.$all.pipe(
    map((payload:any) => {
      if (payload){
        return payload.rusaplannercontrols
      } else {
        return of(null)
      }
    })
  );

  $buttonDisable = combineLatest(([this.$track,this.$controls])).pipe(
    map(([track,controls]) => {
      if (controls && controls.length > 0){
        return controls.some(e => {
          return e.rusaControlRepresentation.CONTROL_DISTANCE_KM >= track.EVENT_DISTANCE_KM;
        });
      } else {
        return false;
      }
    })
  )

  constructor(private trackbuildercomponentService: TrackBuilderComponentService) { }

  ngOnInit(): void {
  }

  createTrack() {
    this.trackbuildercomponentService.createTrack();
  }
}
