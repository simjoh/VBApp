import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import {TrackAdminComponentService} from "../track-admin-component.service";
import {EventRepresentation} from "../../../shared/api/api";
import {map} from "rxjs/operators";
import {cdkMigrations} from "@angular/cdk/schematics";

@Component({
  selector: 'brevet-track-list',
  templateUrl: './track-list.component.html',
  styleUrls: ['./track-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackListComponent implements OnInit {

  $eventsandtrack = this.trackadmincomponentservice.$eventsAndTrack;

  constructor(private trackadmincomponentservice: TrackAdminComponentService,private cd: ChangeDetectorRef) { }

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

  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }
}
