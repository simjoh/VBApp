import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService} from "primeng/dynamicdialog";
import {ConfirmationService, MenuItem} from "primeng/api";

@Component({
    selector: 'brevet-event-admin',
    templateUrl: './event-admin.component.html',
    styleUrls: ['./event-admin.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [DialogService, ConfirmationService],
    standalone: false
})
export class EventAdminComponent implements OnInit {

  designTabs = [];

  constructor() { }

  ngOnInit(): void {

    this.designTabs = [
      {
        label: "Event",
        routerLink: 'brevet-event-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }

}
