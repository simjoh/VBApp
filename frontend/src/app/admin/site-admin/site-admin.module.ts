import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SiteListComponent } from './site-list/site-list.component';
import { SiteAdminComponent } from './site-admin.component';
import {SharedModule} from "../../shared/shared.module";
import { SiteInfoPopoverComponent } from './site-info-popover/site-info-popover.component';



@NgModule({
  declarations: [
    SiteAdminComponent,
    SiteListComponent,
    SiteInfoPopoverComponent
  ],
  imports: [
    CommonModule,
    SharedModule
  ],
  exports: [SiteListComponent]
})
export class SiteAdminModule { }
