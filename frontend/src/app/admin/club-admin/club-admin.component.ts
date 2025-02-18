import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {MenuItem} from "primeng/api";
import {ClubAdminComponentService} from "./club-admin-component.service";

@Component({
    selector: 'brevet-club-admin',
    templateUrl: 'club-admin.component.html',
    styleUrls: ['club-admin.component.scss'],
    providers: [ClubAdminComponentService],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
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
