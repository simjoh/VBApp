import {Component, OnInit, ChangeDetectionStrategy, ViewEncapsulation, ViewChild, ChangeDetectorRef} from '@angular/core';
import { NgForm} from '@angular/forms';
import {DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import {User, UserInfoRepresentation} from 'src/app/shared/api/api';
import {Roles} from "../../../shared/roles";
import { Role } from 'src/app/core/auth/roles';


@Component({
  selector: 'brevet-edit-user-dialog',
  templateUrl: './edit-user-dialog.component.html',
  styleUrls: ['./edit-user-dialog.component.scss']
  // Temporarily removed OnPush to test if change detection is the issue
})
export class EditUserDialogComponent implements OnInit {

  userForm: UserFormModel;
  originalUser: User;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig, private cdr: ChangeDetectorRef) {
    this.originalUser = this.config.data.user;
    console.log('Edit dialog - Original user data:', this.originalUser);
  }

  ngOnInit(): void {
    this.userForm = this.createObjectFromUser(this.originalUser);
    console.log('Edit dialog - User form created:', this.userForm);
    console.log('Edit dialog - User form roles:', {
      superuser: this.userForm.superuser,
      admin: this.userForm.admin,
      user: this.userForm.user,
      developer: this.userForm.developer,
      volonteer: this.userForm.volonteer
    });
    // Trigger change detection to ensure form is updated
    this.cdr.detectChanges();

    // Add a small delay to ensure the form is fully initialized
    setTimeout(() => {
      this.cdr.detectChanges();
    }, 100);
  }

  generatePassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+';
    let password = '';
    for (let i = 0; i < 12; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    this.userForm.password = password;
  }

  updateUser(contactForm: NgForm) {
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
        role_name: Role.DEVELOPER
      })
    }

    if (form.controls.volonteer.value == true){
      roles.push({
        id: Roles.VOLONTAR.valueOf(),
        role_name: Role.VOLONTEER
      })
    }

    // Ensure at least USER role is assigned if no roles are selected
    if (roles.length === 0) {
      console.log('Edit dialog - No roles selected, adding default USER role');
      roles.push({
        id: Roles.USER.valueOf(),
        role_name: Role.USER
      });
    }

    console.log('Edit dialog - Final roles array:', roles);

    let userinfo = {
      phone: form.controls.phone.value,
      email: form.controls.email.value
    } as UserInfoRepresentation

    return {
      user_uid: this.originalUser.user_uid,
      givenname: form.controls.givenname.value,
      familyname: form.controls.familyname.value,
      username: form.controls.username.value,
      token: this.originalUser.token,
      roles: roles,
      userInfoRepresentation: userinfo,
      password: form.controls.password?.value || this.userForm.password || ''
    } as unknown as User;
  }

    private createObjectFromUser(user: User): UserFormModel {
    console.log('Edit dialog - User parameter:', user);
    console.log('Edit dialog - User is null/undefined:', user == null);

    if (!user) {
      console.error('Edit dialog - User object is null or undefined!');
      return this.createEmptyForm();
    }

    console.log('Edit dialog - Full user object:', JSON.stringify(user, null, 2));
    console.log('Edit dialog - User object keys:', Object.keys(user));
    console.log('Edit dialog - User type:', typeof user);
    console.log('Edit dialog - User properties:');
    console.log('  - user_uid:', user.user_uid);
    console.log('  - userUid:', (user as any).userUid);
    console.log('  - givenname:', user.givenname);
    console.log('  - given_name:', (user as any).given_name);
    console.log('  - familyname:', user.familyname);
    console.log('  - family_name:', (user as any).family_name);
    console.log('  - username:', user.username);
    console.log('  - user_name:', (user as any).user_name);
    console.log('  - roles:', user.roles);
    console.log('  - userInfoRepresentation:', user.userInfoRepresentation);

    const userForm = {
      user_uid: user.user_uid || (user as any).userUid || (user as any).user_uid || "",
      givenname: user.givenname || (user as any).given_name || (user as any).givenname || "",
      familyname: user.familyname || (user as any).family_name || (user as any).familyname || "",
      username: user.username || (user as any).user_name || (user as any).username || "",
      superuser: false,
      user: false,
      volonteer: false,
      admin: false,
      developer: false,
      phone: user.userInfoRepresentation?.phone || (user as any).phone || (user as any).userInfoRepresentation?.phone || "",
      email: user.userInfoRepresentation?.email || (user as any).email || (user as any).userInfoRepresentation?.email || "",
      password: ''
    } as UserFormModel;

    console.log('Edit dialog - Created user form:', userForm);

    // Set role checkboxes based on existing roles
    if (user.roles && Array.isArray(user.roles)) {
      console.log('Edit dialog - Processing roles:', user.roles);
      user.roles.forEach((role: any) => {
        console.log('Edit dialog - Processing role:', role);
        // Handle both role_id and id properties for compatibility
        const roleId = role.id || role.role_id;
        console.log('Edit dialog - Role ID:', roleId);
        switch(roleId) {
          case Roles.SUPERUSER.valueOf():
            console.log('Edit dialog - Setting superuser to true');
            userForm.superuser = true;
            break;
          case Roles.ADMIN.valueOf():
            console.log('Edit dialog - Setting admin to true');
            userForm.admin = true;
            break;
          case Roles.USER.valueOf():
            console.log('Edit dialog - Setting user to true');
            userForm.user = true;
            break;
          case Roles.DEVELOPER.valueOf():
            console.log('Edit dialog - Setting developer to true');
            userForm.developer = true;
            break;
          case Roles.VOLONTAR.valueOf():
            console.log('Edit dialog - Setting volonteer to true');
            userForm.volonteer = true;
            break;
          default:
            console.log('Edit dialog - Unknown role ID:', roleId);
        }
      });
    } else {
      console.log('Edit dialog - No roles found or roles is not an array:', user.roles);
      // If user has no roles, default to USER role
      console.log('Edit dialog - Setting default USER role');
      userForm.user = true;
    }

    return userForm;
  }

  private createEmptyForm(): UserFormModel {
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
      password: ""
    } as UserFormModel;
  }
}

export class UserFormModel {
  user_uid;
  givenname;
  familyname;
  username;
  superuser: boolean;
  user: boolean;
  volonteer: boolean;
  admin: boolean;
  developer: boolean;
  phone;
  email;
  password;
}
