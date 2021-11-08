import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {UserAdminComponentService} from "./user-admin-component.service";

@Component({
  selector: 'brevet-user-admin',
  templateUrl: './user-admin.component.html',
  styleUrls: ['./user-admin.component.scss'],
  providers: [UserAdminComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UserAdminComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
