import {ChangeDetectionStrategy, Component, OnInit, ViewEncapsulation} from '@angular/core';
import {MenuItem} from "primeng/api";
import {TrackAdminComponentService} from "./track-admin-component.service";

@Component({
    selector: 'brevet-track-admin',
    templateUrl: './track-admin.component.html',
    styleUrls: ['./track-admin.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [TrackAdminComponentService],
    standalone: false
})
export class TrackAdminComponent implements OnInit {

  designTabs = [];
  tabs = [];

  constructor(private tra: TrackAdminComponentService) { }

  ngOnInit(): void {

    this.tra.init();

    this.tabs = [{
      id: 1,
      header: 'Tab 1'
    }, {
      id: 2,
      header: 'Tab 2'
    }];

    this.designTabs = [
      {
        label: "Banor",
        routerLink: 'brevet-track-list',
        icon: 'pi pi-list'
      },
      {
        label: "Ladda upp banor",
        routerLink: 'brevet-track-upload',
        icon: 'pi pi-fw pi-upload'
      },
      {
        label: "Banbyggare",
        routerLink: 'brevet-track-builder',
      }
    ] as MenuItem[];
  }

}
