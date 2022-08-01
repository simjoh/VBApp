import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {UserAdminComponentService} from "./user-admin-component.service";
import {MenuItem} from "primeng/api";

@Component({
  selector: 'brevet-user-admin',
  templateUrl: './user-admin.component.html',
  styleUrls: ['./user-admin.component.scss'],
  providers: [UserAdminComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UserAdminComponent implements OnInit {

  designTabs = [];

  constructor() { }

  ngOnInit(): void {

    this.designTabs = [
      {
        label: "Användare",
        routerLink: 'brevet-track-list',
        icon: 'pi pi-list'
      },
      {
        label: "BehörighetsProfiler",
        routerLink: 'brevet-permissions',
        icon: 'pi pi-lock'
      }
    ] as MenuItem[];
  }

}
