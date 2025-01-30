import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {AdministrationComponentService} from "./administration-component.service";
import {MenuItem} from "primeng/api";
import {map} from "rxjs/operators";
import {Roles} from "../../shared/roles";

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


  $activetabs = this.administrationcomponentservice.$activeUser.pipe(
    map(aciveuser => {

      if (aciveuser.roles.find(s => s.id === Roles.ADMIN)) {
        this.designTabs.push({
          label: "Förbered acp rapport",
          routerLink: 'prepare-report',
          icon: 'pi pi-list'
        })
      }

      if (aciveuser.roles.find(s => s.id === Roles.ACPREPRESENTIVE)) {
        this.designTabs.push({
          label: "Rapportera till acp",
          routerLink: 'to-report',
          icon: 'pi pi-list'
        })
      }

    })
  ).subscribe();

  constructor(public administrationcomponentservice: AdministrationComponentService) {

  }

  ngOnInit(): void {

  }

}
