import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService} from "primeng/dynamicdialog";
import {ConfirmationService, MenuItem} from "primeng/api";
import {PageHeaderConfig} from '../../shared/components/page-header/page-header.component';

@Component({
  selector: 'brevet-event-admin',
  templateUrl: './event-admin.component.html',
  styleUrls: ['./event-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers:[DialogService, ConfirmationService]
})
export class EventAdminComponent implements OnInit {

  designTabs = [];
  
  headerConfig: PageHeaderConfig = {
    icon: 'pi pi-calendar',
    title: 'Hantera Arrangemangsgrupp',
    description: 'Hantera grupper av arrangemang'
  };

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
