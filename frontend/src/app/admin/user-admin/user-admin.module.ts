import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserAdminRoutingModule } from './user-admin-routing.module';
import { CreateUserDialogComponent } from './create-user-dialog/create-user-dialog.component';
import { SharedModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    CreateUserDialogComponent
  ],
  exports: [CreateUserDialogComponent],
  imports: [
    CommonModule,
    SharedModule,
    UserAdminRoutingModule
  ]
})
export class UserAdminModule { }
