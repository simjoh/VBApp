import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CompetitorRoutingModule } from './competitor-routing.module';
import { CompetitorComponent } from './competitor.component';
import {CoreModule} from "../core/core.module";
import {MapComponent} from "./map/map.component";
import { ListComponent } from './list/list.component';
import {CardModule} from "primeng/card";
import {ButtonModule} from "primeng/button";
import {SharedModule} from "../shared/shared.module";


@NgModule({
  declarations: [
    CompetitorComponent,
    MapComponent,
    ListComponent
  ],
  imports: [
    SharedModule,
    CommonModule,
    CoreModule,
    CompetitorRoutingModule,
  ],
  exports: [MapComponent]
})
export class CompetitorModule { }
