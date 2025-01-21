import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {OrganizerService} from "./organizer.service";
import {MenuItem} from "primeng/api";
import {RouterOutlet} from "@angular/router";
import {TabMenuModule} from "primeng/tabmenu";
import {map} from "rxjs/operators";
import {EventRepresentation, OrganizerRepresentation} from "../../shared/api/api";
import {Observable} from "rxjs";

@Component({
  selector: 'brevet-organizer-admin',
  standalone: true,
  imports: [
    RouterOutlet,
    TabMenuModule
  ],
  templateUrl: './organizer-admin.component.html',
  styleUrl: './organizer-admin.component.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class OrganizerAdminComponent implements OnInit {

  designTabs = [];


  constructor(private organizerservice: OrganizerService) {
  }

  ngOnInit(): void {
    this.organizerservice.getAllOrganizers();
    this.designTabs = [
      {
        label: "Arrangörer",
        routerLink: 'brevet-organizer-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }

}
