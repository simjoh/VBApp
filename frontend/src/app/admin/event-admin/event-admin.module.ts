import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { EventAdminRoutingModule } from './event-admin-routing.module';
import { EventAdminComponent } from './event-admin.component';
import { EventListComponent } from './event-list/event-list.component';
import { EventInfoPopoverComponent } from './event-info-popover/event-info-popover.component';
import { CreateEventDialogComponent } from './create-event-dialog/create-event-dialog.component';
import { EditEventDialogComponent } from './edit-event-dialog/edit-event-dialog.component';
import { SharedModule } from '../../shared/shared.module';
import { CardModule } from 'primeng/card';
import { DialogModule } from 'primeng/dialog';
import { DynamicDialogModule } from 'primeng/dynamicdialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { InputTextareaModule } from 'primeng/inputtextarea';
import { RadioButtonModule } from 'primeng/radiobutton';
import { CalendarModule } from 'primeng/calendar';

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
    FormsModule,
    SharedModule,
    EventAdminRoutingModule,
    CardModule,
    DialogModule,
    DynamicDialogModule,
    ButtonModule,
    InputTextModule,
    InputTextareaModule,
    RadioButtonModule,
    CalendarModule
  ]
})
export class EventAdminModule { }
