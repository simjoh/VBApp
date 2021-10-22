import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {CardModule} from "primeng/card";
import {ButtonModule} from "primeng/button";



@NgModule({
  declarations: [],
  imports: [
    CommonModule,
    CardModule,
    ButtonModule,
    FormsModule, ReactiveFormsModule
  ], exports: [CommonModule, FormsModule, ReactiveFormsModule, CardModule,ButtonModule]
})
export class SharedModule { }
