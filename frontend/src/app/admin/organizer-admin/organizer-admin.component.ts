import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { DialogService } from "primeng/dynamicdialog";
import { ConfirmationService, MenuItem } from "primeng/api";

@Component({
  selector: 'brevet-organizer-admin',
  templateUrl: './organizer-admin.component.html',
  styleUrls: ['./organizer-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [DialogService, ConfirmationService]
})
export class OrganizerAdminComponent implements OnInit {

  designTabs = [];

  constructor() { }

  ngOnInit(): void {
    this.designTabs = [
      {
        label: "Organisatörer",
        routerLink: 'brevet-organizer-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }
}
