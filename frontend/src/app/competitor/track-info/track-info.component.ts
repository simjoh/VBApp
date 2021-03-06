import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
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

  @Input() track_uid: string

  $track =  this.authService.$auth$.pipe(
    mergeMap((auth) => {
      if (this.track_uid){
        return this.trackService.getTrack(this.track_uid)
      } else {
        return this.trackService.getTrack(auth.trackuid)
      }

    }),
    map((test: any) => {
      return test;
    })
  );

  constructor(private authService: AuthService, private trackService: TrackService) { }

  ngOnInit(): void {
  }

}
