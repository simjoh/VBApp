import {ChangeDetectionStrategy, Component, OnInit, ViewEncapsulation} from '@angular/core';
import {MenuItem} from 'primeng/api';
import {ParticipantComponentService} from "../participant-component.service";

@Component({
  selector: 'brevet-participant',
  templateUrl: './participant.component.html',
  styleUrls: ['./participant.component.scss'],
  providers: [ParticipantComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class ParticipantComponent implements OnInit {

  designTabs = [];

  constructor() { }

  ngOnInit(): void {

    this.designTabs = [
      {
        label: "Lista",
        routerLink: 'brevet-participant-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }

}
