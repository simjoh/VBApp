import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { MenuItem } from 'primeng/api';
import {ParticipantComponentService} from "../participant-component.service";

@Component({
  selector: 'brevet-participant',
  templateUrl: './participant.component.html',
  styleUrls: ['./participant.component.scss'],
  providers: [ParticipantComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantComponent implements OnInit {

  designTabs = [];

  constructor() { }

  ngOnInit(): void {

    this.designTabs = [
      {
        label: "Deltagare",
        routerLink: 'om-ds',
      },
      {
        label: "Ladda upp deltagare",
        routerLink: 'brevet-participant-upload',
        icon: 'pi pi-fw pi-upload'
      },
      {
        label: "Lista deltagare",
        routerLink: 'brevet-participant-list'
      },
      {
        label: "Administerar deltagare",
        routerLink: 'sokning',
        styleClass: 'ds-beta-tab'
      }
    ] as MenuItem[];
    // this.items = [
    //   {label: 'Home', icon: 'pi pi-fw pi-home'},
    //   {label: 'Calendar', icon: 'pi pi-fw pi-calendar'},
    //   {label: 'Edit', icon: 'pi pi-fw pi-pencil'},
    //   {label: 'Documentation', icon: 'pi pi-fw pi-file'},
    //   {label: 'Settings', icon: 'pi pi-fw pi-cog'}
    // ];
  }

}
