import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { OrganizerAdminRoutingModule } from './organizer-admin-routing.module';
import {SharedModule} from "../../shared/shared.module";


@NgModule({
  declarations: [],
  imports: [
    CommonModule,
    SharedModule,
    OrganizerAdminRoutingModule
  ]
})
export class OrganizerAdminModule { }
