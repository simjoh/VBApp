import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {AuthService} from "../../core/auth/auth.service";
import {map, mergeMap} from "rxjs/operators";
import {TrackService} from "../track.service";

@Component({
  selector: 'brevet-track-info',
  templateUrl: './track-info.component.html',
  styleUrls: ['./track-info.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackInfoComponent implements OnInit {

  $track =  this.authService.$auth$.pipe(
    mergeMap((auth) => {
      return this.trackService.getTrack(auth.trackuid)
    }),
    map((test: any) => {
      return test;
    })
  );

  constructor(private authService: AuthService, private trackService: TrackService) { }

  ngOnInit(): void {
  }

}
