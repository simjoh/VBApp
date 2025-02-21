import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CardModule } from 'primeng/card';
import { ButtonModule } from 'primeng/button';
import { TableModule } from 'primeng/table';
import { InputTextModule } from 'primeng/inputtext';
import { CalendarModule } from 'primeng/calendar';
import { InputNumberModule } from 'primeng/inputnumber';
import { TooltipModule } from 'primeng/tooltip';
import { TabMenuModule } from 'primeng/tabmenu';
import { FileUploadModule } from 'primeng/fileupload';
import { AccordionModule } from 'primeng/accordion';
import { TabViewModule } from 'primeng/tabview';
import { PanelModule } from 'primeng/panel';
import { MenuModule } from 'primeng/menu';
import { ImageModule } from 'primeng/image';

import { TrackRoutingModule } from './track-routing.module';
import { TrackAdminComponent } from './track-admin.component';
import { UploadTrackComponent } from './upload-track/upload-track.component';
import { TrackListComponent } from './track-list/track-list.component';
import { SharedModule } from '../../shared/shared.module';
import { TrackInfoPopoverComponent } from './track-info-popover/track-info-popover.component';
import { TrackBuilderComponent } from './track-builder/track-builder.component';
import { TrackBuilderTrackInfoFormComponent } from './track-builder/track-builder-track-info-form/track-builder-track-info-form.component';
import { TrackBuilderControlsFormComponent } from './track-builder/track-builder-controls-form/track-builder-controls-form.component';
import { TrackBuilderSummaryComponent } from './track-builder/track-builder-summary/track-builder-summary.component';
import { TrackBuilderComponentService } from './track-builder/track-builder-component.service';

@NgModule({
  declarations: [
    TrackAdminComponent,
    UploadTrackComponent,
    TrackListComponent,
    TrackInfoPopoverComponent
  ],
  exports: [
    TrackInfoPopoverComponent,
    TrackListComponent
  ],
  imports: [
    CommonModule,
    TrackRoutingModule,
    SharedModule,
    CardModule,
    ButtonModule,
    TableModule,
    InputTextModule,
    CalendarModule,
    InputNumberModule,
    TooltipModule,
    TabMenuModule,
    FileUploadModule,
    AccordionModule,
    TabViewModule,
    PanelModule,
    MenuModule,
    ImageModule,
    TrackBuilderSummaryComponent,
    TrackBuilderComponent,
    TrackBuilderTrackInfoFormComponent,
    TrackBuilderControlsFormComponent
  ],
  providers: [
    TrackBuilderComponentService
  ]
})
export class TrackModule { }
