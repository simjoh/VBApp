import {Component, OnInit, ChangeDetectionStrategy, ViewEncapsulation, ViewChild} from '@angular/core';
import {NgForm} from '@angular/forms';
import {DynamicDialogConfig, DynamicDialogRef} from 'primeng/dynamicdialog';
import {User, UserInfoRepresentation} from 'src/app/shared/api/api';
import {Roles} from "../../../shared/roles";
import {Role} from 'src/app/core/auth/roles';
import {AuthService} from "../../../core/auth/auth.service";


@Component({
    selector: 'brevet-create-user-dialog',
    templateUrl: './create-user-dialog.component.html',
    styleUrls: ['./create-user-dialog.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class CreateUserDialogComponent implements OnInit {

  userForm: any = {
    givenname: '',
    familyname: '',
    username: '',
    phone: '',
    email: '',
    superuser: false,
    acprepresentant: false,
    developer: false,
    admin: false,
    user: false,
    volonteer: false
  };

  roles: any[] = [];

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {
  }

  ngOnInit(): void {
    this.roles = this.config.data.userrole;
  }

  addUser(form: NgForm) {
    if (form.valid) {
      this.ref.close(this.userForm);
    }
  }

  cancel() {
    this.ref.close(null);
  }

  private getUserObject(form: NgForm): User {

    let roles = Array<any>();

    if (form.controls.superuser.value === true) {
      roles.push({
        id: Roles.SUPERUSER.valueOf(),
        role_name: Role.SUPERUSER
      })
    }

    if (form.controls.admin.value == true) {
      roles.push({
        id: Roles.ADMIN.valueOf(),
        role_name: Role.ADMIN
      })
    }

    if (form.controls.user.value == true) {
      roles.push({
        id: Roles.USER.valueOf(),
        role_name: Role.USER
      })
    }

    if (form.controls.developer.value == true) {
      roles.push({
        id: Roles.DEVELOPER.valueOf(),
        role_name: Role.ADMIN
      })
    }

    if (form.controls.volonteer.value == true) {
      roles.push({
        id: Roles.VOLONTAR.valueOf(),
        role_name: Role.VOLONTEER
      })
    }

    let userinfo = {
      phone: form.controls.phone.value,
      email: form.controls.email.value
    } as UserInfoRepresentation

    return {
      user_uid: "",
      givenname: form.controls.givenname.value,
      familyname: form.controls.familyname.value,
      username: form.controls.username.value,
      token: "",
      roles: roles,
      userInfoRepresentation: userinfo
    } as unknown as User;


    return null;
  }

  private createObject(): UserFormModel {

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
      acprepresentant: false,
      phone: "",
      email: "",
    } as UserFormModel;

  }

  canAddRole(role: string): boolean {
    return true; // Implement your role check logic here
  }

  protected readonly Role = Role;


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
  acprepresentant: false;
  phone;
  email;
}
