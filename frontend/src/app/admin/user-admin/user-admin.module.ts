import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserAdminRoutingModule } from './user-admin-routing.module';
import { CreateUserDialogComponent } from './create-user-dialog/create-user-dialog.component';
import { SharedModule } from 'src/app/shared/shared.module';
import { UserInfoPopoverComponent } from './user-info-popover/user-info-popover.component';
import {SiteAdminModule} from "../site-admin/site-admin.module";


@NgModule({
  declarations: [
    CreateUserDialogComponent,
    UserInfoPopoverComponent
  ],
  exports: [CreateUserDialogComponent, UserInfoPopoverComponent],
  imports: [
    CommonModule,
    SharedModule,
    UserAdminRoutingModule,

  ]
})
export class UserAdminModule { }
