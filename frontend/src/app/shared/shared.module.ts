import { NgModule } from '@angular/core';
import {CommonModule, DatePipe} from '@angular/common';
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {CardModule} from "primeng/card";
import {ButtonModule} from "primeng/button";
import { LogoComponent } from './logo/logo.component';



@NgModule({
  declarations: [
    LogoComponent
  ],
  imports: [
    CommonModule,
    CardModule,
    ButtonModule,
    FormsModule, ReactiveFormsModule
  ], exports: [CommonModule, FormsModule, ReactiveFormsModule, CardModule, ButtonModule, LogoComponent,DatePipe],
  providers: [DatePipe]
})
export class SharedModule { }
