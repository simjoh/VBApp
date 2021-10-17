import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CompetitorRoutingModule } from './competitor-routing.module';
import { CompetitorComponent } from './competitor.component';
import {CoreModule} from "../core/core.module";


@NgModule({
  declarations: [
    CompetitorComponent
  ],
  imports: [
    CommonModule,
    CoreModule,
    CompetitorRoutingModule
  ]
})
export class CompetitorModule { }
