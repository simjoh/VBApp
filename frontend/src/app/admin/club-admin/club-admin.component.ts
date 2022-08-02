import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {MenuItem} from "primeng/api";

@Component({
  selector: 'brevet-club-admin',
  templateUrl: 'club-admin.component.html',
  styleUrls: ['club-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ClubAdminComponent implements OnInit {

  designTabs = [];

  constructor() { }

  ngOnInit(): void {

    this.designTabs = [
      {
        label: "Klubbar",
        routerLink: 'brevet-club-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }

}
