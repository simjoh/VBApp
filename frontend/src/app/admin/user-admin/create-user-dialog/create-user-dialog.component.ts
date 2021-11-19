import { Component, OnInit, ChangeDetectionStrategy, ViewEncapsulation } from '@angular/core';
import {DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import { User } from 'src/app/shared/api/api';

@Component({
  selector: 'brevet-create-user-dialog',
  templateUrl: './create-user-dialog.component.html',
  styleUrls: ['./create-user-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateUserDialogComponent implements OnInit {

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {


    console.log(config);
  }

  ngOnInit(): void {
  }

  addUser() {
    this.ref.close(this.createUserObject());
  }

  cancel(){
    this.ref.close(null);
  }


  private createUserObject(): User{
    return {
      user_uid: "",
      givenname: "Andreas",
      familyname: "User",
      username: "andrease@user",
      token: "",
      roles: ["ADMIN"]
    } as User;
  }


}
