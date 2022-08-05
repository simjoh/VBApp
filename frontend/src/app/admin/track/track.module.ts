import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { TrackRoutingModule } from './track-routing.module';
import { TrackAdminComponent } from './track-admin.component';
import { UploadTrackComponent } from './upload-track/upload-track.component';
import {TabMenuModule} from "primeng/tabmenu";
import {FileUploadModule} from "primeng/fileupload";
import { TrackListComponent } from './track-list/track-list.component';
import {SharedModule} from "../../shared/shared.module";
import {AccordionModule} from "primeng/accordion";
import {TabViewModule} from "primeng/tabview";
import {PanelModule} from "primeng/panel";
import {MenuModule} from "primeng/menu";
import {TableModule} from "primeng/table";
import { TrackInfoPopoverComponent } from './track-info-popover/track-info-popover.component';
import { TrackBuilderComponent } from './track-builder/track-builder.component';



@NgModule({
  declarations: [
    TrackAdminComponent,
    UploadTrackComponent,
    TrackListComponent,
    TrackInfoPopoverComponent,
    TrackBuilderComponent
  ],
  exports: [
    TrackInfoPopoverComponent
  ],
  imports: [
    TabMenuModule,
    TrackRoutingModule,
    FileUploadModule,
    SharedModule
  ]
})
export class TrackModule { }
