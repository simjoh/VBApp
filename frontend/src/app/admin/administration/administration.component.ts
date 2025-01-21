import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {AdministrationComponentService} from "./administration-component.service";
import {MenuItem} from "primeng/api";

@Component({
  selector: 'brevet-administration',
  templateUrl: './administration.component.html',
  styleUrls: ['./administration.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [AdministrationComponentService]
})
export class AdministrationComponent implements OnInit {


  designTabs = [];
  tabs = [];

  constructor(administrationcomponentservice: AdministrationComponentService) {

  }

  ngOnInit(): void {
    this.tabs = [{
      id: 1,
      header: 'Tab 1'
    }, {
      id: 2,
      header: 'Tab 2'
    }];

    this.designTabs = [
      {
        label: "Acp-rapport",
        routerLink: 'acpreport',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }

}
