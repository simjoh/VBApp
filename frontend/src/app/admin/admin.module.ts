import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdminRoutingModule } from './admin-routing.module';
import { AdminComponent } from './admin.component';
import {CoreModule} from "../core/core.module";
import { UserAdminComponent } from './user-admin/user-admin.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { UserListComponent } from './user-admin/user-list/user-list.component';


@NgModule({
  declarations: [
    AdminComponent,
    UserAdminComponent,
    DashboardComponent,
    UserListComponent
  ],
  imports: [
    CommonModule,
    CoreModule,
    AdminRoutingModule
  ]
})
export class AdminModule { }
