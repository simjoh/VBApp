import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { Router } from '@angular/router';
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

  constructor(
    private trackadmincomponentservice: TrackAdminComponentService,
    private cd: ChangeDetectorRef,
    private router: Router
  ) { }

  ngOnInit(): void {
    // Parent component already calls init(), no need to call it again
    // this.trackadmincomponentservice.init();
  }

  isPossibleToDelete(event: EventRepresentation) {
    const result = this.trackadmincomponentservice.deletelinkExists(event);
    // Force change detection update
    setTimeout(() => {
      this.cd.markForCheck();
    });
    return result;
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

  navigateToBuilder() {
    this.router.navigate(['/admin/banor/brevet-track-builder'], { queryParams: { mode: 'create' } });
  }
}
