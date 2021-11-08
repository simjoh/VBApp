import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {UserAdminComponentService} from "../user-admin-component.service";

@Component({
  selector: 'brevet-user-list',
  templateUrl: './user-list.component.html',
  styleUrls: ['./user-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UserListComponent implements OnInit {

  $users = this.userService.$users;

  constructor(private userService: UserAdminComponentService) { }

  ngOnInit(): void {
  }

}
