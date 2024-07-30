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
import { ParticipantListComponent } from './participant-list/participant-list.component';
import { ParticipantTableComponent } from './participant-table/participant-table.component';
import { ParticipantCheckpointTableComponent } from './participant-checkpoint-table/participant-checkpoint-table.component';
import { EditTimeDialogComponent } from './edit-time-dialog/edit-time-dialog.component';
import { EditBrevenrDialogComponent } from './edit-brevenr-dialog/edit-brevenr-dialog.component';

@NgModule({
  declarations: [
    ParticipantComponent,
    UploadParticipantComponent,
    TrackSelectorComponent,
    ParticipantListComponent,
    ParticipantTableComponent,
    ParticipantCheckpointTableComponent,
    EditTimeDialogComponent,
    EditBrevenrDialogComponent
  ],
  imports: [
    ParticipantRoutingModule,
    TabMenuModule,
    FileUploadModule,
    CommonModule,
    InputTextModule,
    FormsModule,
    DropdownModule,
    SharedModule,
  ],
  exports: []
})
export class ParticipantModule { }
