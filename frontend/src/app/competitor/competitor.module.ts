import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CompetitorRoutingModule } from './competitor-routing.module';
import { CompetitorComponent } from './competitor.component';
import {CoreModule} from "../core/core.module";
import {MapComponent} from "./map/map.component";


@NgModule({
  declarations: [
    CompetitorComponent,
    MapComponent,
  ],
  imports: [
    CommonModule,
    CoreModule,
    CompetitorRoutingModule
  ],
  exports: [MapComponent]
})
export class CompetitorModule { }
