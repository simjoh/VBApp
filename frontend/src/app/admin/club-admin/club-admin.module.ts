import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ClubAdminRoutingModule } from './club-admin-routing.module';
import { ClubAdminComponent } from './club-admin.component';
import { ClubListComponent } from './club-list/club-list.component';
import { CreateClubDialogComponent } from './create-club-dialog/create-club-dialog.component';
import { EditClubDialogComponent } from './edit-club-dialog/edit-club-dialog.component';
import { SharedModule } from '../../shared/shared.module';
import { CardModule } from 'primeng/card';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { TableModule } from 'primeng/table';
import { TooltipModule } from 'primeng/tooltip';

@NgModule({
  declarations: [
    ClubAdminComponent,
    ClubListComponent,
    CreateClubDialogComponent,
    EditClubDialogComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    SharedModule,
    ClubAdminRoutingModule,
    CardModule,
    DialogModule,
    ButtonModule,
    InputTextModule,
    TableModule,
    TooltipModule
  ]
})
export class ClubAdminModule { }
