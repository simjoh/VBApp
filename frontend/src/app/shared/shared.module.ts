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
import {DialogService, DynamicDialogModule} from "primeng/dynamicdialog";
import {InputTextModule} from "primeng/inputtext";
import {TooltipModule} from "primeng/tooltip";
import {MessageModule} from "primeng/message";
import {OverlayPanelModule} from "primeng/overlaypanel";
import { OverlayComponent } from './components/overlay/overlay.component';
import { YesNoPipe } from './pipes/yes-no.pipe';
import { TimeAgoPipe } from './pipes/time-ago.pipe';
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
import { DateTimePrettyPrintPipe } from './pipes/date-time-pretty-print.pipe';
import { RemoveAfterPipe } from './pipes/remove-after.pipe';
import {AccordionModule} from "primeng/accordion";
import {TabViewModule} from "primeng/tabview";
import {MenuModule} from "primeng/menu";
import { TrackTableComponent } from './components/track-table/track-table.component';
import { CheckpointTableComponent } from './components/checkpoint-table/checkpoint-table.component';
import { HtmlLinkComponent } from './components/html-link/html-link.component';
import {CheckpointPreviewComponent} from "./components/checkpoint-preview/checkpoint-preview.component";
import {
  CheckpointPreviewDialogComponent
} from "./components/checkpoint-preview/checkpoint-preview-dialog/checkpoint-preview-dialog.component";
import {CheckpointComponent} from "./components/checkpoint/checkpoint.component";
import {TrackInfoComponent} from "./components/track-info/track-info.component";
import { TracksForEventSelectorComponent } from './components/tracks-for-event-selector/tracks-for-event-selector.component';
import {InputNumberModule} from "primeng/inputnumber";
import { EventSelectorComponent } from './components/event-selector/event-selector.component';
import { SiteSelectorComponent } from './components/site-selector/site-selector.component';
import {ToolbarModule} from "primeng/toolbar";
import {StepsModule} from "primeng/steps";
import {CheckboxModule} from "primeng/checkbox";
import { OrganizerSelectorComponent } from './organizer-selector/organizer-selector.component';
import { TranslationPipe } from './pipes/translation.pipe';

// New Compact Components
import { PageHeaderComponent } from './components/page-header/page-header.component';
import { ActionCardComponent } from './components/action-card/action-card.component';
import { PageLayoutComponent } from './components/page-layout/page-layout.component';
import { CompactPageHeaderComponent } from './components/compact-page-header/compact-page-header.component';
import { CompactActionCardComponent } from './components/compact-action-card/compact-action-card.component';

@NgModule({
    declarations: [
        LogoComponent,
        OverlayComponent,
        YesNoPipe,
        TimeAgoPipe,
        DistanceBetweenPipe,
        CloseOrOpenPipe,
        DistanceKmPipe,
        DatetimeBetweenPipe,
        ToastComponent,
        DateTimePrettyPrintPipe,
        RemoveAfterPipe,
        TrackTableComponent,
        CheckpointTableComponent,
        HtmlLinkComponent,
        CheckpointPreviewComponent,
        CheckpointPreviewDialogComponent,
        CheckpointComponent,
        TrackInfoComponent,
        TracksForEventSelectorComponent,
        EventSelectorComponent,
        SiteSelectorComponent,
        OrganizerSelectorComponent,
        // New Compact Components
        PageHeaderComponent,
        ActionCardComponent,
        PageLayoutComponent,
        CompactPageHeaderComponent,
        CompactActionCardComponent
    ],
    imports: [
        CommonModule,
        NgbCollapseModule,
        ProgressSpinnerModule,
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
        TabViewModule,
        PanelModule,
        MenuModule,
        FormsModule, ReactiveFormsModule, StepsModule, AccordionModule, InputNumberModule, ToolbarModule,
        TranslationPipe
    ],
    exports: [CommonModule, ToastModule, ConfirmPopupModule, TabViewModule,
        PanelModule, ToolbarModule,
        MenuModule, BadgeModule,CheckboxModule, StepsModule ,ListboxModule, SiteSelectorComponent, EventSelectorComponent, InputNumberModule, TracksForEventSelectorComponent, CheckpointComponent, CheckpointPreviewComponent, CheckpointPreviewDialogComponent, KnobModule, HtmlLinkComponent, ListboxModule, PanelModule, InputTextModule, TagModule, TabMenuModule, ImageModule, FormsModule, MenubarModule, ReactiveFormsModule, CardModule, TooltipModule, ButtonModule,
        LogoComponent, DatePipe, NgbCollapseModule, ProgressSpinnerModule, AccordionModule, TableModule, MultiSelectModule, SliderModule, OverlayPanelModule,
        ProgressBarModule, TrackTableComponent, DropdownModule, MessageModule, ConfirmDialogModule, DynamicDialogModule, RippleModule, DialogModule, RadioButtonModule, OverlayComponent, YesNoPipe, TimeAgoPipe, CalendarModule, InputTextareaModule, FileUploadModule, DistanceBetweenPipe, DistanceKmPipe, DatetimeBetweenPipe, ToastComponent, DateTimePrettyPrintPipe, RemoveAfterPipe, TracksForEventSelectorComponent, CheckpointTableComponent, OrganizerSelectorComponent, TranslationPipe,
        // New Compact Components
        PageHeaderComponent,
        ActionCardComponent,
        PageLayoutComponent,
        CompactPageHeaderComponent,
        CompactActionCardComponent],
    providers: [DatePipe, DialogService]
})
export class SharedModule { }
