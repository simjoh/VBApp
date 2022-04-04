import { NgModule } from '@angular/core';


import { ParticipantRoutingModule } from './participant-routing.module';
import { ParticipantComponent } from './participant/participant.component';
import { UploadParticipantComponent } from './upload-participant/upload-participant.component';
import {TabMenuModule} from "primeng/tabmenu";
import {FileUploadModule} from "primeng/fileupload";
import {SharedModule} from "../../shared/shared.module";
import {CommonModule} from "@angular/common";
import {InputTextModule} from "primeng/inputtext";
import {AppModule} from "../../app.module";
import {FormsModule} from "@angular/forms";
import { TrackSelectorComponent } from './track-selector/track-selector.component';
import {DropdownModule} from "primeng/dropdown";

@NgModule({
  declarations: [
    ParticipantComponent,
    UploadParticipantComponent,
    TrackSelectorComponent
  ],
  imports: [
    ParticipantRoutingModule,
    TabMenuModule,
    FileUploadModule,
    CommonModule,
    InputTextModule,
    FormsModule,
    DropdownModule,
  ],
  exports: []
})
export class ParticipantModule { }
