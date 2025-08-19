import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService} from "primeng/dynamicdialog";
import {ConfirmationService, MenuItem} from "primeng/api";
import {CompactPageHeaderConfig} from '../../shared/components';

@Component({
  selector: 'brevet-site-admin',
  templateUrl: './site-admin.component.html',
  styleUrls: ['./site-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers:[DialogService, ConfirmationService]
})
export class SiteAdminComponent implements OnInit {

  designTabs = [];

  headerConfig: CompactPageHeaderConfig = {
    icon: 'pi pi-map-marker',
    title: 'Hantera Kontrollplatser',
    description: 'Hantera och organisera alla kontrollplatser'
  };

  constructor() { }

  ngOnInit(): void {

    this.designTabs = [
      {
        label: "Platser",
        routerLink: 'brevet-site-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }

}
