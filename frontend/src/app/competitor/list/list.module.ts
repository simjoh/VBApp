import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {ListComponent} from "./list.component";
import {SharedModule} from "../../shared/shared.module";
import {CompetitorModule} from "../competitor.module";
import {CoreModule} from "../../core/core.module";
import {TrackInfoComponent} from "../track-info/track-info.component";



@NgModule({
  declarations: [ListComponent, TrackInfoComponent],
	imports: [
		CommonModule,
		SharedModule,
		CompetitorModule,
		CoreModule,

	],
	exports: [
		ListComponent
	]
})
export class ListModule { }
