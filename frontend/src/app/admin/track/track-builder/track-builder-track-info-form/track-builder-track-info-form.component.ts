import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {TrackBuilderComponentService} from "../track-builder-component.service";

@Component({
  selector: 'brevet-track-builder-track-info-form',
  templateUrl: './track-builder-track-info-form.component.html',
  styleUrls: ['./track-builder-track-info-form.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackBuilderTrackInfoFormComponent implements OnInit {

  model = new EventTrackInfo(0,"", "", "", "", "");

  constructor(private trackbuildercomponentService: TrackBuilderComponentService) { }

  ngOnInit(): void {
  }

  addEvent($event: any) {
        this.model.event_uid = $event;
        this.trackbuildercomponentService.choosenEvent(this.model.event_uid);
  }


  add() {
    this.trackbuildercomponentService.rusaInput(
      {
        event_distance: this.model.trackdistance,
        start_time: this.model.starttime,
        start_date: this.model.startdate,
        event_uid: "",
        track_title: this.model.trackname,
        controls: [],
        link: this.model.link
      }
    )
  }
}

export class EventTrackInfo {
  constructor(
    public trackdistance: number,
    public trackname: string,
    public event_uid: string,
    public starttime?: string,
    public startdate?: string,
    public link?: string,
  ) {  }

}
