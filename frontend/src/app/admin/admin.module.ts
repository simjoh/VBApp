import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AdminRoutingModule } from './admin-routing.module';
import { AdminComponent } from './admin.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { AdminDashboardComponent } from './admin-dashboard/admin-dashboard.component';
import {SharedModule} from "../shared/shared.module";
import {CheckboxModule} from "primeng/checkbox";
import {EventAdminModule} from "./event-admin/event-admin.module";
import { AdminStartComponent } from './admin-start/admin-start.component';
import { AcpRapportComponent } from './acp-rapport/acp-rapport.component';
import {UserAdminModule} from "./user-admin/user-admin.module";
import { CardModule } from 'primeng/card';
import { TableModule } from 'primeng/table';
import { TagModule } from 'primeng/tag';
import { ProgressSpinnerModule } from 'primeng/progressspinner';
import { ButtonModule } from 'primeng/button';
import { DropdownModule } from 'primeng/dropdown';
import { TooltipModule } from 'primeng/tooltip';

@NgModule({
  declarations: [
    AdminComponent,
    DashboardComponent,
    AdminDashboardComponent,
    AdminStartComponent,
    AcpRapportComponent,
  ],
  imports: [
    CommonModule,
    FormsModule,
    SharedModule,
    CheckboxModule,
    EventAdminModule,
    AdminRoutingModule,
    UserAdminModule,
    CardModule,
    TableModule,
    TagModule,
    ProgressSpinnerModule,
    ButtonModule,
    DropdownModule,
    TooltipModule
  ],
  exports: [AdminComponent]
})
export class AdminModule { }
