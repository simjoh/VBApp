import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {TrackBuilderComponentService} from "../track-builder-component.service";
import {map, mergeMap, switchMap} from "rxjs/operators";
import {Observable, of} from "rxjs";

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
  )

  constructor(private trackbuildercomponentService: TrackBuilderComponentService) { }

  ngOnInit(): void {
  }

}
