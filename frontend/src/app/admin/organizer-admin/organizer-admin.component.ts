import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { DialogService } from "primeng/dynamicdialog";
import { ConfirmationService, MenuItem } from "primeng/api";
import {CompactPageHeaderConfig} from '../../shared/components';

@Component({
  selector: 'brevet-organizer-admin',
  templateUrl: './organizer-admin.component.html',
  styleUrls: ['./organizer-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [DialogService, ConfirmationService]
})
export class OrganizerAdminComponent implements OnInit {

  designTabs = [];

  headerConfig: CompactPageHeaderConfig = {
    icon: 'pi pi-briefcase',
    title: 'Hantera Arrangörer',
    description: 'Hantera de som arrangerar loppen'
  };

  constructor() { }

  ngOnInit(): void {
    this.designTabs = [
      {
        label: "Arrangörer",
        routerLink: 'brevet-organizer-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }
}
