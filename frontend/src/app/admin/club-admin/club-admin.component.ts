import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { DialogService } from "primeng/dynamicdialog";
import { ConfirmationService, MenuItem } from "primeng/api";

@Component({
  selector: 'brevet-club-admin',
  templateUrl: 'club-admin.component.html',
  styleUrls: ['club-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [DialogService, ConfirmationService]
})
export class ClubAdminComponent implements OnInit {

  designTabs = [];

  constructor() { }

  ngOnInit(): void {

    this.designTabs = [
      {
        label: "Klubblista",
        routerLink: 'brevet-club-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }

}
