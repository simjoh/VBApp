import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdminRoutingModule } from './admin-routing.module';
import { AdminComponent } from './admin.component';
import {CoreModule} from "../core/core.module";
import { UserAdminComponent } from './user-admin/user-admin.component';
import { DashboardComponent } from './dashboard/dashboard.component';


@NgModule({
  declarations: [
    AdminComponent,
    UserAdminComponent,
    DashboardComponent
  ],
  imports: [
    CommonModule,
    CoreModule,
    AdminRoutingModule
  ]
})
export class AdminModule { }
