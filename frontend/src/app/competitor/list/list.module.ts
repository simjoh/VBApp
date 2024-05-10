import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {ListComponent} from "./list.component";
import {SharedModule} from "../../shared/shared.module";
import {CompetitorModule} from "../competitor.module";
import { CheckpointComponent } from 'src/app/shared/components/checkpoint/checkpoint.component';
import {CoreModule} from "../../core/core.module";



@NgModule({
  declarations: [ListComponent],
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
