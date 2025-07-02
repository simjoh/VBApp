import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserAdminRoutingModule } from './user-admin-routing.module';
import { CreateUserDialogComponent } from './create-user-dialog/create-user-dialog.component';
import { EditUserDialogComponent } from './edit-user-dialog/edit-user-dialog.component';
import { SharedModule } from 'src/app/shared/shared.module';
import { UserInfoPopoverComponent } from './user-info-popover/user-info-popover.component';
import { PermissionAdminComponent } from './permission-admin/permission-admin.component';
import { UserAdminComponent } from './user-admin.component';

// PrimeNG Imports
import { TableModule } from 'primeng/table';
import { CardModule } from 'primeng/card';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { RippleModule } from 'primeng/ripple';
import { TooltipModule } from 'primeng/tooltip';
import { ConfirmDialogModule } from 'primeng/confirmdialog';
import { ToastModule } from 'primeng/toast';
import { CheckboxModule } from 'primeng/checkbox';

@NgModule({
  declarations: [
    UserAdminComponent,
    CreateUserDialogComponent,
    EditUserDialogComponent,
    UserInfoPopoverComponent,
    PermissionAdminComponent
  ],
  exports: [
    UserAdminComponent,
    CreateUserDialogComponent,
    EditUserDialogComponent,
    UserInfoPopoverComponent
  ],
  imports: [
    CommonModule,
    SharedModule,
    UserAdminRoutingModule,
    // PrimeNG Modules
    TableModule,
    CardModule,
    ButtonModule,
    InputTextModule,
    RippleModule,
    TooltipModule,
    ConfirmDialogModule,
    ToastModule,
    CheckboxModule
  ]
})
export class UserAdminModule { }
