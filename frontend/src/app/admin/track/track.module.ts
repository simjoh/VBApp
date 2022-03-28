import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { TrackRoutingModule } from './track-routing.module';
import { TrackAdminComponent } from './track-admin/track-admin.component';
import {SharedModule} from "../../shared/shared.module";
import { UploadTrackComponent } from './upload-track/upload-track.component';
import {TabMenuModule} from "primeng/tabmenu";
import {FileUploadModule} from "primeng/fileupload";


@NgModule({
  declarations: [
    TrackAdminComponent,
    UploadTrackComponent
  ],
  imports: [
    TabMenuModule,
    TrackRoutingModule,
    FileUploadModule,

  ]
})
export class TrackModule { }
