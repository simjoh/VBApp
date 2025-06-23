import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserAdminRoutingModule } from './user-admin-routing.module';
import { CreateUserDialogComponent } from './create-user-dialog/create-user-dialog.component';
import { EditUserDialogComponent } from './edit-user-dialog/edit-user-dialog.component';
import { SharedModule } from 'src/app/shared/shared.module';
import { UserInfoPopoverComponent } from './user-info-popover/user-info-popover.component';
import { PermissionAdminComponent } from './permission-admin/permission-admin.component';
import { CheckboxModule } from 'primeng/checkbox';


@NgModule({
  declarations: [
    CreateUserDialogComponent,
    EditUserDialogComponent,
    UserInfoPopoverComponent,
    PermissionAdminComponent
  ],
  exports: [CreateUserDialogComponent, EditUserDialogComponent, UserInfoPopoverComponent],
  imports: [
    CommonModule,
    SharedModule,
    UserAdminRoutingModule,
    CheckboxModule
  ]
})
export class UserAdminModule { }
