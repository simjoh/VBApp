import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {EventAdminComponent} from "./event-admin.component";
import { EventListComponent } from './event-list/event-list.component';
import {SharedModule} from "../../shared/shared.module";
import { EventInfoPopoverComponent } from './event-info-popover/event-info-popover.component';
import { CreateEventDialogComponent } from './create-event-dialog/create-event-dialog.component';




@NgModule({
  declarations: [
   EventAdminComponent,
   EventListComponent,
   EventInfoPopoverComponent,
   CreateEventDialogComponent
  ],
  imports: [
    CommonModule,
    SharedModule
  ]
})
export class EventAdminModule { }
