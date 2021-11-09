import { NgModule } from '@angular/core';
import {CommonModule, DatePipe} from '@angular/common';
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {CardModule} from "primeng/card";
import {ButtonModule} from "primeng/button";
import { LogoComponent } from './logo/logo.component';
import {TabMenuModule} from 'primeng/tabmenu';
import {MenubarModule} from 'primeng/menubar';
import {NgbCollapseModule} from "@ng-bootstrap/ng-bootstrap";
import {ProgressSpinnerModule} from 'primeng/progressspinner';




@NgModule({
  declarations: [
    LogoComponent
  ],
  imports: [
    CommonModule,
    NgbCollapseModule,
    ProgressSpinnerModule,
    CardModule,
    ButtonModule,
    TabMenuModule,
    MenubarModule,
    FormsModule, ReactiveFormsModule
  ], exports: [CommonModule,TabMenuModule,FormsModule, MenubarModule,ReactiveFormsModule, CardModule, ButtonModule, LogoComponent,DatePipe,NgbCollapseModule, ProgressSpinnerModule],
  providers: [DatePipe]
})
export class SharedModule { }
