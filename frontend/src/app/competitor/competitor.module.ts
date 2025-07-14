import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CompetitorRoutingModule } from './competitor-routing.module';
import { CompetitorComponent } from './competitor.component';
import {MapComponent} from "./map/map.component";
import {SharedModule} from "../shared/shared.module";
import { TrackInfoComponent } from './track-info/track-info.component';
import {ListModule} from "./list/list.module";

@NgModule({
  declarations: [
    CompetitorComponent,
    MapComponent,
    TrackInfoComponent,
  ],
  imports: [
    SharedModule,
    CommonModule,
    CompetitorRoutingModule,
  ],
  exports: [MapComponent, TrackInfoComponent]
})
export class CompetitorModule { }
