import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { TableModule } from 'primeng/table';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { InputTextareaModule } from 'primeng/inputtextarea';
import { SelectButtonModule } from 'primeng/selectbutton';
import { ToggleButtonModule } from 'primeng/togglebutton';
import { RadioButtonModule } from 'primeng/radiobutton';
import { DialogModule } from 'primeng/dialog';
import { DynamicDialogModule } from 'primeng/dynamicdialog';
import { ConfirmDialogModule } from 'primeng/confirmdialog';
import { ConfirmationService, MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { TagModule } from 'primeng/tag';
import { TooltipModule } from 'primeng/tooltip';
import { RippleModule } from 'primeng/ripple';
import { PaginatorModule } from 'primeng/paginator';
import { TabMenuModule } from 'primeng/tabmenu';

import { OrganizerAdminRoutingModule } from './organizer-admin-routing.module';
import { OrganizerAdminComponent } from './organizer-admin.component';
import { OrganizerListComponent } from './organizer-list/organizer-list.component';
import { CreateOrganizerDialogComponent } from './create-organizer-dialog/create-organizer-dialog.component';
import { EditOrganizerDialogComponent } from './edit-organizer-dialog/edit-organizer-dialog.component';

@NgModule({
  declarations: [
    OrganizerAdminComponent,
    OrganizerListComponent,
    CreateOrganizerDialogComponent,
    EditOrganizerDialogComponent
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    OrganizerAdminRoutingModule,
    TableModule,
    ButtonModule,
    InputTextModule,
    InputTextareaModule,
    SelectButtonModule,
    ToggleButtonModule,
    RadioButtonModule,
    DialogModule,
    DynamicDialogModule,
    ConfirmDialogModule,
    ToastModule,
    TagModule,
    TooltipModule,
    RippleModule,
    PaginatorModule,
    TabMenuModule
  ],
  providers: [
    ConfirmationService,
    MessageService
  ]
})
export class OrganizerAdminModule { }
