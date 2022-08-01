import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdminRoutingModule } from './admin-routing.module';
import { AdminComponent } from './admin.component';
import {CoreModule} from "../core/core.module";
import { UserAdminComponent } from './user-admin/user-admin.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { UserListComponent } from './user-admin/user-list/user-list.component';
import {SharedModule} from "../shared/shared.module";
import {CreateUserDialogComponent} from "./user-admin/create-user-dialog/create-user-dialog.component";
import {CheckboxModule} from "primeng/checkbox";
import {UserInfoPopoverComponent} from "./user-admin/user-info-popover/user-info-popover.component";
import {SiteAdminModule} from "./site-admin/site-admin.module";
import {EventAdminModule} from "./event-admin/event-admin.module";
import { AdminStartComponent } from './admin-start/admin-start.component';
import {TrackModule} from "./track/track.module";
import {UserAdminModule} from "./user-admin/user-admin.module";


@NgModule({
  declarations: [
    AdminComponent,
    UserAdminComponent,
    DashboardComponent,
    UserListComponent,
    AdminStartComponent,
  ],
  imports: [
    CommonModule,
    CoreModule,
    SharedModule,
    SiteAdminModule,
    CheckboxModule,
    EventAdminModule,
    AdminRoutingModule,
    UserAdminModule,
  ],
    exports: [AdminComponent]
})
export class AdminModule { }
