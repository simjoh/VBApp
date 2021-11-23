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
import {DialogModule} from "primeng/dialog";
import {DynamicDialogModule} from "primeng/dynamicdialog";
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import {InputTextModule} from "primeng/inputtext";
import {TooltipModule} from "primeng/tooltip";
import {MessageModule} from "primeng/message";
import {OverlayPanelModule} from "primeng/overlaypanel";
import { OverlayComponent } from './components/overlay/overlay.component';
import {SiteListComponent} from "../admin/site-admin/site-list/site-list.component";

@NgModule({
    declarations: [
        LogoComponent,
        OverlayComponent,
    ],
  entryComponents: [],
  imports: [
    CommonModule,
    NgbCollapseModule,
    ProgressSpinnerModule,
    BrowserAnimationsModule,
    MultiSelectModule,
    SliderModule,
    DropdownModule,
    ConfirmDialogModule,
    DialogModule,
    DynamicDialogModule,
    ProgressBarModule,
    RippleModule,
    RadioButtonModule,
    TableModule,
    CardModule,
    ButtonModule,
    TooltipModule,
    TabMenuModule,
    MenubarModule,
    InputTextModule,
    MessageModule,
    OverlayPanelModule,
    FormsModule, ReactiveFormsModule
  ],
    exports: [CommonModule, InputTextModule, TabMenuModule, FormsModule, MenubarModule, ReactiveFormsModule, CardModule, TooltipModule, ButtonModule,
        LogoComponent, DatePipe, NgbCollapseModule, BrowserAnimationsModule, ProgressSpinnerModule, TableModule, MultiSelectModule, SliderModule, OverlayPanelModule,
        ProgressBarModule, DropdownModule, MessageModule, ConfirmDialogModule, DynamicDialogModule, RippleModule, DialogModule, RadioButtonModule, OverlayComponent],
  providers: [DatePipe]
})
export class SharedModule { }
