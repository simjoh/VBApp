import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ClubAdminRoutingModule } from './club-admin-routing.module';
import { ClubListComponent } from './club-list/club-list.component';
import { ClubAdminComponent } from './club-admin.component';
import { CreateClubDialogComponent } from './create-club-dialog/create-club-dialog.component';
import { EditClubDialogComponent } from './edit-club-dialog/edit-club-dialog.component';
import { SharedModule } from "../../shared/shared.module";

@NgModule({
  declarations: [
    ClubAdminComponent,
    ClubListComponent,
    CreateClubDialogComponent,
    EditClubDialogComponent
  ],
  imports: [
    CommonModule,
    SharedModule,
    ClubAdminRoutingModule
  ]
})
export class ClubAdminModule { }
