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
import { YesNoPipe } from './pipes/yes-no.pipe';
import {CalendarModule} from "primeng/calendar";
import {InputTextareaModule} from "primeng/inputtextarea";
import {FileUploadModule} from "primeng/fileupload";
import {ImageModule} from "primeng/image";
import { DistanceBetweenPipe } from './pipes/distance-between.pipe';
import { CloseOrOpenPipe } from './pipes/close-or-open.pipe';
import { TagModule } from 'primeng/tag';
import {PanelModule} from "primeng/panel";
import { DistanceKmPipe } from './pipes/distance-km.pipe';
import { DatetimeBetweenPipe } from './pipes/datetime-between.pipe';
import {ListboxModule} from 'primeng/listbox';
import {BadgeModule} from "primeng/badge";
import {KnobModule} from "primeng/knob";
import {ConfirmPopupModule} from 'primeng/confirmpopup';
import {ToastModule} from 'primeng/toast';
import { ToastComponent } from './components/toast/toast.component';
import {BrowserModule} from "@angular/platform-browser";
import { DateTimePrettyPrintPipe } from './pipes/date-time-pretty-print.pipe';
import { RemoveAfterPipe } from './pipes/remove-after.pipe';
import {ChartModule} from "primeng/chart";

@NgModule({
    declarations: [
        LogoComponent,
        OverlayComponent,
        YesNoPipe,
        DistanceBetweenPipe,
        CloseOrOpenPipe,
        DistanceKmPipe,
        DatetimeBetweenPipe,
        ToastComponent,
        DateTimePrettyPrintPipe,
        RemoveAfterPipe,
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
    ImageModule,
    TagModule,
    InputTextModule,
    MessageModule,
    OverlayPanelModule,
    CalendarModule,
    InputTextareaModule,
    FileUploadModule,
    PanelModule,
    DropdownModule,
    KnobModule,
    BadgeModule,
    ListboxModule,
    ConfirmPopupModule,
    ToastModule,
    BrowserModule,
    FormsModule, ReactiveFormsModule
  ],
  exports: [CommonModule, BrowserModule ,ToastModule, ConfirmPopupModule, BadgeModule, KnobModule, ListboxModule, PanelModule, InputTextModule, TagModule, TabMenuModule, ImageModule, FormsModule, MenubarModule, ReactiveFormsModule, CardModule, TooltipModule, ButtonModule,
    LogoComponent, DatePipe, NgbCollapseModule, BrowserAnimationsModule, ProgressSpinnerModule, TableModule, MultiSelectModule, SliderModule, OverlayPanelModule,
    ProgressBarModule, DropdownModule, MessageModule, ConfirmDialogModule, DynamicDialogModule, RippleModule, DialogModule, RadioButtonModule, OverlayComponent, YesNoPipe, CalendarModule, InputTextareaModule, FileUploadModule, DistanceBetweenPipe, DistanceKmPipe, DatetimeBetweenPipe, ToastComponent, DateTimePrettyPrintPipe, RemoveAfterPipe],
  providers: [DatePipe]
})
export class SharedModule { }
