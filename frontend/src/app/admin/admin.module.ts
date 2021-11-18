import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdminRoutingModule } from './admin-routing.module';
import { AdminComponent } from './admin.component';
import {CoreModule} from "../core/core.module";
import { UserAdminComponent } from './user-admin/user-admin.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { UserListComponent } from './user-admin/user-list/user-list.component';
import {SharedModule} from "../shared/shared.module";
import {TableModule} from "primeng/table";
import {CreateUserDialogComponent} from "./user-admin/create-user-dialog/create-user-dialog.component";
import {CheckboxModule} from "primeng/checkbox";


@NgModule({
  declarations: [
    AdminComponent,
    UserAdminComponent,
    DashboardComponent,
    UserListComponent,
    CreateUserDialogComponent
  ],
  imports: [
    CommonModule,
    CoreModule,
    AdminRoutingModule,
    SharedModule,
    CheckboxModule
  ],
  exports: [SharedModule]
})
export class AdminModule { }
