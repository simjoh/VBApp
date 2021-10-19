import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VolunteerRoutingModule } from './volunteer-routing.module';
import { VolunteerComponent } from './volunteer/volunteer.component';


@NgModule({
  declarations: [
    VolunteerComponent
  ],
  imports: [
    CommonModule,
    VolunteerRoutingModule
  ]
})
export class VolunteerModule { }
