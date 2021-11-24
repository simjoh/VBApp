import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService} from "primeng/dynamicdialog";
import {ConfirmationService} from "primeng/api";

@Component({
  selector: 'brevet-event-admin',
  templateUrl: './event-admin.component.html',
  styleUrls: ['./event-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers:[DialogService, ConfirmationService]
})
export class EventAdminComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
