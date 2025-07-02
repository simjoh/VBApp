import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdminRoutingModule } from './admin-routing.module';
import { AdminComponent } from './admin.component';
import {CoreModule} from "../core/core.module";
import { DashboardComponent } from './dashboard/dashboard.component';
import {SharedModule} from "../shared/shared.module";
import {CheckboxModule} from "primeng/checkbox";
import {EventAdminModule} from "./event-admin/event-admin.module";
import { AdminStartComponent } from './admin-start/admin-start.component';
import {UserAdminModule} from "./user-admin/user-admin.module";
import { CardModule } from 'primeng/card';
import { TableModule } from 'primeng/table';
import { TagModule } from 'primeng/tag';
import { ProgressSpinnerModule } from 'primeng/progressspinner';

@NgModule({
  declarations: [
    AdminComponent,
    DashboardComponent,
    AdminStartComponent,
  ],
  imports: [
    CommonModule,
    CoreModule,
    SharedModule,
    CheckboxModule,
    EventAdminModule,
    AdminRoutingModule,
    UserAdminModule,
    CardModule,
    TableModule,
    TagModule,
    ProgressSpinnerModule
  ],
  exports: [AdminComponent]
})
export class AdminModule { }
