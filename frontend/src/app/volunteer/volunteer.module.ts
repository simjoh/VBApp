import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VolunteerRoutingModule } from './volunteer-routing.module';
import { VolunteerComponent } from './volunteer/volunteer.component';
import {SharedModule} from "../shared/shared.module";
import { EventChooserComponent } from './event-chooser/event-chooser.component';
import { TrackSelectorComponent } from './track-selector/track-selector.component';
import { CheckpointSelectorComponent } from './checkpoint-selector/checkpoint-selector.component';
import { CheckpointSelectorListboxComponent } from './checkpoint-selector-listbox/checkpoint-selector-listbox.component';
import { ParticipantListComponent } from './participant-list/participant-list.component';


@NgModule({
  declarations: [
    VolunteerComponent,
    EventChooserComponent,
    TrackSelectorComponent,
    CheckpointSelectorComponent,
    CheckpointSelectorListboxComponent,
    ParticipantListComponent
  ],
  imports: [
    CommonModule,
    SharedModule,
    VolunteerRoutingModule

  ]
})
export class VolunteerModule { }
