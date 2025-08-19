import {Component, OnInit, ChangeDetectionStrategy, ViewEncapsulation, ViewChild} from '@angular/core';
import { NgForm} from '@angular/forms';
import {DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import {User, UserInfoRepresentation} from 'src/app/shared/api/api';
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

  generatePassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+';
    let password = '';
    for (let i = 0; i < 12; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    this.userForm.password = password;
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
      userInfoRepresentation: userinfo,
      password: form.controls.password?.value || this.userForm.password || '',
      organizer_id: form.controls.organizer_id?.value || this.userForm.organizer_id
    } as unknown as User;




    return null;
  }

  private createObject(): UserFormModel{

    // Get current user from localStorage
    const currentUser = JSON.parse(localStorage.getItem('activeUser') || '{}');
    const isSuperUser = currentUser.roles?.includes('SUPERUSER');

    console.log('Create user dialog - Current user:', currentUser);
    console.log('Create user dialog - Current user roles:', currentUser.roles);
    console.log('Create user dialog - Is superuser:', isSuperUser);
    console.log('Create user dialog - Current user organizer_id:', currentUser.organizer_id);
    console.log('Create user dialog - Raw localStorage activeUser:', localStorage.getItem('activeUser'));

    // Preselect organizer_id if user is not superuser and has an organizer_id
    let preselectedOrganizerId: number | undefined = undefined;
    if (!isSuperUser && currentUser.organizer_id) {
      preselectedOrganizerId = currentUser.organizer_id;
      console.log('Create user dialog - Preselecting organizer_id:', preselectedOrganizerId);
    } else {
      console.log('Create user dialog - Not preselecting organizer_id. isSuperUser:', isSuperUser, 'has organizer_id:', !!currentUser.organizer_id);
    }

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
      phone: "",
      email: "",
      password: "",
      organizer_id: preselectedOrganizerId
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
  phone;
  email;
  password;
  organizer_id?: number;
}
