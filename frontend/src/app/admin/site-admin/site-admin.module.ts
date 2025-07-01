import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { SiteAdminRoutingModule } from './site-admin-routing.module';
import { SiteAdminComponent } from './site-admin.component';
import { SiteListComponent } from './site-list/site-list.component';
import { SiteInfoPopoverComponent } from './site-info-popover/site-info-popover.component';
import { CreateSiteDialogComponent } from './create-site-dialog/create-site-dialog.component';
import { EditSiteDialogComponent } from './edit-site-dialog/edit-site-dialog.component';
import { SharedModule } from '../../shared/shared.module';
import { CardModule } from 'primeng/card';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';

@NgModule({
  declarations: [
    SiteAdminComponent,
    SiteListComponent,
    SiteInfoPopoverComponent,
    CreateSiteDialogComponent,
    EditSiteDialogComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    SharedModule,
    SiteAdminRoutingModule,
    CardModule,
    DialogModule,
    ButtonModule,
    InputTextModule
  ]
})
export class SiteAdminModule { }
