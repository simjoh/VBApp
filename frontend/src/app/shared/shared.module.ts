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
import {TableModule} from 'primeng/table';
import {MultiSelectModule} from "primeng/multiselect";
import {SliderModule} from "primeng/slider";
import {DropdownModule} from "primeng/dropdown";
import {ProgressBarModule} from "primeng/progressbar";
import {ConfirmDialogModule} from "primeng/confirmdialog";
import {RippleModule} from "primeng/ripple";
import {RadioButtonModule} from "primeng/radiobutton";





@NgModule({
  declarations: [
    LogoComponent
  ],
  imports: [
    CommonModule,
    NgbCollapseModule,
    ProgressSpinnerModule,
    MultiSelectModule,
    SliderModule,
    DropdownModule,
    ConfirmDialogModule,
    ProgressBarModule,
    RippleModule,
    RadioButtonModule,
    TableModule,
    CardModule,
    ButtonModule,
    TabMenuModule,
    MenubarModule,
    FormsModule, ReactiveFormsModule
  ], exports: [CommonModule,TabMenuModule,FormsModule, MenubarModule,ReactiveFormsModule, CardModule, ButtonModule,
    LogoComponent,DatePipe,NgbCollapseModule, ProgressSpinnerModule,TableModule,MultiSelectModule,SliderModule, ProgressBarModule,DropdownModule, ConfirmDialogModule, RippleModule, RadioButtonModule],
  providers: [DatePipe]
})
export class SharedModule { }
