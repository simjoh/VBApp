import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {EventAdminComponent} from "./event-admin.component";
import { EventListComponent } from './event-list/event-list.component';
import {SharedModule} from "../../shared/shared.module";
import { EventInfoPopoverComponent } from './event-info-popover/event-info-popover.component';
import { CreateEventDialogComponent } from './create-event-dialog/create-event-dialog.component';
import {EventAdminRoutingModule} from "./event-admin-routing.module";
import { EditEventDialogComponent } from './edit-event-dialog/edit-event-dialog.component';


@NgModule({
  declarations: [
   EventAdminComponent,
   EventListComponent,
   EventInfoPopoverComponent,
   CreateEventDialogComponent,
   EditEventDialogComponent
  ],
  imports: [
    CommonModule,
    SharedModule,
    EventAdminRoutingModule
  ]
})
export class EventAdminModule { }
