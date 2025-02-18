import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import {map, mergeMap} from "rxjs/operators";
import { TrackService } from 'src/app/competitor/track.service';
import { AuthService } from 'src/app/core/auth/auth.service';


@Component({
    selector: 'brevet-track-info',
    templateUrl: './track-info.component.html',
    styleUrls: ['./track-info.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
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
