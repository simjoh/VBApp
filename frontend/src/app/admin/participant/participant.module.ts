import { NgModule } from '@angular/core';


import { ParticipantRoutingModule } from './participant-routing.module';
import { ParticipantComponent } from './participant/participant.component';
import { UploadParticipantComponent } from './upload-participant/upload-participant.component';
import {TabMenuModule} from "primeng/tabmenu";
import {FileUploadModule} from "primeng/fileupload";
import {SharedModule} from "../../shared/shared.module";
import {CommonModule} from "@angular/common";

@NgModule({
  declarations: [
    ParticipantComponent,
    UploadParticipantComponent
  ],
  imports: [
    ParticipantRoutingModule,
    TabMenuModule,
    FileUploadModule,
    CommonModule
  ],
  exports: []
})
export class ParticipantModule { }
