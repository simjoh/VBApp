import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService} from "primeng/dynamicdialog";
import {ConfirmationService} from "primeng/api";

@Component({
  selector: 'brevet-site-admin',
  templateUrl: './site-admin.component.html',
  styleUrls: ['./site-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers:[DialogService, ConfirmationService]
})
export class SiteAdminComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
