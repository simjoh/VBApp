import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {ListComponent} from "./list.component";
import {SharedModule} from "../../shared/shared.module";
import { CheckpointComponent } from './checkpoint/checkpoint.component';



@NgModule({
  declarations: [ListComponent, CheckpointComponent],
  imports: [
    CommonModule,
    SharedModule
  ],
  exports:[]
})
export class ListModule { }
