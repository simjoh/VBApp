import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {MenuItem} from "primeng/api";

@Component({
  selector: 'brevet-track-admin',
  templateUrl: './track-admin.component.html',
  styleUrls: ['./track-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackAdminComponent implements OnInit {

  designTabs = [];

  constructor() { }

  ngOnInit(): void {

    this.designTabs = [
      {
        label: "Banor",
        routerLink: 'om-ds',
      },
      {
        label: "Ladda upp banor",
        routerLink: 'brevet-track-upload',
        icon: 'pi pi-fw pi-upload'
      }
    ] as MenuItem[];
  }

}
