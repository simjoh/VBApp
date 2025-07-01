import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { OrganizerAdminRoutingModule } from './organizer-admin-routing.module';
import { OrganizerAdminComponent } from './organizer-admin.component';
import { OrganizerListComponent } from './organizer-list/organizer-list.component';
import { CreateOrganizerDialogComponent } from './create-organizer-dialog/create-organizer-dialog.component';
import { EditOrganizerDialogComponent } from './edit-organizer-dialog/edit-organizer-dialog.component';
import { SharedModule } from '../../shared/shared.module';
import { CardModule } from 'primeng/card';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';

@NgModule({
  declarations: [
    OrganizerAdminComponent,
    OrganizerListComponent,
    CreateOrganizerDialogComponent,
    EditOrganizerDialogComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    SharedModule,
    OrganizerAdminRoutingModule,
    CardModule,
    DialogModule,
    ButtonModule,
    InputTextModule
  ]
})
export class OrganizerAdminModule { }
