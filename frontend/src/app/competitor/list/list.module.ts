import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {ListComponent} from "./list.component";
import {SharedModule} from "../../shared/shared.module";
import { CheckpointComponent } from './checkpoint/checkpoint.component';
import {CompetitorModule} from "../competitor.module";
import {TrackInfoComponent} from "../track-info/track-info.component";



@NgModule({
  declarations: [ListComponent, CheckpointComponent],
  imports: [
    CommonModule,
    SharedModule,
    CompetitorModule,
  ],
    exports: [
        ListComponent
    ]
})
export class ListModule { }
