import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {TrackAdminComponentService} from "../track-admin-component.service";
import {EventRepresentation} from "../../../shared/api/api";

@Component({
  selector: 'brevet-track-list',
  templateUrl: './track-list.component.html',
  styleUrls: ['./track-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackListComponent implements OnInit {

  $eventsandtrack = this.trackadmincomponentservice.$eventsAndTrack;

  constructor(private trackadmincomponentservice: TrackAdminComponentService) { }

  ngOnInit(): void {
    this.trackadmincomponentservice.init();
  }

  isPossibleToDelete(event: EventRepresentation) {
    return this.trackadmincomponentservice.deletelinkExists(event);
  }

  remove(event: EventRepresentation) {
    return this.trackadmincomponentservice.removeEvent(event);
  }

  reload() {
    this.trackadmincomponentservice.init();
  }
}
