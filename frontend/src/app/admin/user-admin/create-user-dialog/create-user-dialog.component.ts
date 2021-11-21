import {Component, OnInit, ChangeDetectionStrategy, ViewEncapsulation, ViewChild} from '@angular/core';
import {FormGroup, NgForm} from '@angular/forms';
import {DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import { User } from 'src/app/shared/api/api';
import {THIS_EXPR} from "@angular/compiler/src/output/output_ast";
import {Roles} from "../../../shared/roles";
import { Role } from 'src/app/core/auth/roles';


@Component({
  selector: 'brevet-create-user-dialog',
  templateUrl: './create-user-dialog.component.html',
  styleUrls: ['./create-user-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateUserDialogComponent implements OnInit {


  userForm: UserFormModel;


  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {
  }
  ngOnInit(): void {
    this.userForm = this.createObject();
  }

  addUser(contactForm: NgForm) {
    if (contactForm.valid){
      this.ref.close(this.getUserObject(contactForm));
    } else {
      contactForm.dirty
    }
  }
  cancel(){
    this.ref.close(null);
  }

  private getUserObject(form: NgForm): User {

    let roles = Array<any>();

    if (form.controls.superuser.value === true){
      roles.push({
        id: Roles.SUPERUSER.valueOf(),
        role_name: Role.SUPERUSER
      })
    }

    if (form.controls.admin.value == true){
      roles.push({
        id: Roles.ADMIN.valueOf(),
        role_name:  Role.ADMIN
      })
    }

    if (form.controls.user.value == true){
      roles.push({
        id: Roles.USER.valueOf(),
        role_name: Role.USER
      })
    }

    if (form.controls.developer.value == true){
      roles.push({
        id: Roles.DEVELOPER.valueOf(),
        role_name: Role.ADMIN
      })
    }

    if (form.controls.volonteer.value == true){
      roles.push({
        id: Roles.VOLONTAR.valueOf(),
        role_name: Role.VOLONTEER
      })
    }

    return {
      user_uid: "",
      givenname: form.controls.givenname.value,
      familyname: form.controls.familyname.value,
      username: form.controls.username.value,
      token: "",
      roles: roles
    } as unknown as User;




    return null;
  }

  private createObject(): UserFormModel{

    return {
      user_uid: "",
      givenname: "",
      familyname: "",
      username: "",
      superuser: false,
      user: false,
      volonteer: false,
      admin: false,
      developer: false,
    } as UserFormModel;

  }
}






export class UserFormModel {
  user_uid;
  givenname;
  familyname;
  username;
  superuser: false;
  user: false;
  volonteer: false;
  admin: false;
  developer: false;
}
