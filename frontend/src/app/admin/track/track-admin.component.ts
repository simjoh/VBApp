import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {MenuItem} from "primeng/api";
import {TrackAdminComponentService} from "./track-admin-component.service";

@Component({
  selector: 'brevet-track-admin',
  templateUrl: './track-admin.component.html',
  styleUrls: ['./track-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [TrackAdminComponentService]
})
export class TrackAdminComponent implements OnInit {

  designTabs = [];
  tabs = [];

  constructor(private tra: TrackAdminComponentService) { }

  ngOnInit(): void {

    this.tra.init();

    this.tabs = [{
      id: 1,
      header: 'Tab 1'
    }, {
      id: 2,
      header: 'Tab 2'
    }];

    this.designTabs = [
      {
        label: "Banor",
        routerLink: 'brevet-track-list',
        icon: "pi pi-fw pi-directions"
      },
      {
        label: "Ladda upp banor",
        routerLink: 'brevet-track-upload',
        icon: 'pi pi-fw pi-upload'
      }
    ] as MenuItem[];
  }

}
