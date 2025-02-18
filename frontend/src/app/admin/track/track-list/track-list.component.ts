import { BehaviorSubject, Observable } from "rxjs";
import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from "@angular/core";
import { EventRepresentation } from "../../../shared/api/api";
import { TrackAdminComponentService } from "../track-admin-component.service";
import { TableRowCollapseEvent, TableRowExpandEvent } from "primeng/table";

interface Product {
  event: EventRepresentation & { event_uid: string };
  tracks: any[];
  expanded?: boolean;
}

@Component({
  selector: 'brevet-track-list',
  templateUrl: './track-list.component.html',
  styleUrls: ['./track-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  standalone: false
})
export class TrackListComponent implements OnInit {
  $eventsandtrack = this.trackadmincomponentservice.$eventsAndTrack;
  expandedRows: { [key: string]: boolean } = {};

  constructor(private trackadmincomponentservice: TrackAdminComponentService, private cd: ChangeDetectorRef) { }

  ngOnInit(): void {
    this.trackadmincomponentservice.init();
  }

  onRowExpand(event: TableRowExpandEvent) {
    console.log('Row expanded:', event.data);
    const rowData = event.data as Product;
    this.expandedRows = { [rowData.event.event_uid]: true };
    this.cd.detectChanges();
  }

  onRowCollapse(event: TableRowCollapseEvent) {
    console.log('Row collapsed:', event.data);
    const rowData = event.data as Product;
    this.expandedRows = {};
    this.cd.detectChanges();
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