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
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import { TrackSelectorComponent } from './track-selector/track-selector.component';
import {DropdownModule} from "primeng/dropdown";
import { ParticipantListComponent } from './participant-list/participant-list.component';
import { ParticipantTableComponent } from './participant-table/participant-table.component';
import { ParticipantCheckpointTableComponent } from './participant-checkpoint-table/participant-checkpoint-table.component';
import { EditTimeDialogComponent } from './edit-time-dialog/edit-time-dialog.component';
import { EditBrevenrDialogComponent } from './edit-brevenr-dialog/edit-brevenr-dialog.component';
import { EditCheckpointTimeDialogComponent } from './edit-checkpoint-time-dialog/edit-checkpoint-time-dialog.component';
import { EditCompetitorInfoDialogComponent } from './edit-competitor-info-dialog/edit-competitor-info-dialog.component';
import { CalendarModule } from 'primeng/calendar';

@NgModule({
  declarations: [
    ParticipantComponent,
    UploadParticipantComponent,
    TrackSelectorComponent,
    ParticipantListComponent,
    ParticipantTableComponent,
    ParticipantCheckpointTableComponent,
    EditTimeDialogComponent,
    EditBrevenrDialogComponent,
    EditCheckpointTimeDialogComponent,
    EditCompetitorInfoDialogComponent
  ],
  imports: [
    ParticipantRoutingModule,
    TabMenuModule,
    FileUploadModule,
    CommonModule,
    InputTextModule,
    FormsModule,
    ReactiveFormsModule,
    DropdownModule,
    SharedModule,
    CalendarModule
  ],
  exports: []
})
export class ParticipantModule { }
