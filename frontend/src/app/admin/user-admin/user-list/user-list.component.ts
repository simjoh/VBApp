import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {UserAdminComponentService} from "../user-admin-component.service";
import {UserService} from "../user.service";
import {User} from "../../../shared/api/api";
import {Observable} from "rxjs";

@Component({
  selector: 'brevet-user-list',
  templateUrl: './user-list.component.html',
  styleUrls: ['./user-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UserListComponent implements OnInit {

  $users = this.userService.usersWithAdd$ as Observable<User[]>;

  constructor(private userService: UserService) { }

  ngOnInit(): void {
  }

  add() {
    this.userService.newUser(null);
  }
}
