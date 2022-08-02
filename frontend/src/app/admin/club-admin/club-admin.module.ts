import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ClubAdminRoutingModule } from './club-admin-routing.module';
import { ClubListComponent } from './club-list/club-list.component';
import { ClubAdminComponent } from './club-admin.component';
import {SharedModule} from "../../shared/shared.module";


@NgModule({
  declarations: [
    ClubAdminComponent,
    ClubListComponent
  ],
  imports: [
    CommonModule,
    SharedModule,
    ClubAdminRoutingModule
  ]
})
export class ClubAdminModule { }
