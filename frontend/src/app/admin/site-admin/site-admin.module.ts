
import { CommonModule } from '@angular/common';
import {SiteAdminRoutingModule} from "./site-admin-routing.module";
import {NgModule} from "@angular/core";
import { SharedModule } from 'src/app/shared/shared.module';
import {SiteAdminComponent} from "./site-admin.component";
import {SiteListComponent} from "./site-list/site-list.component";
import {SiteInfoPopoverComponent} from "./site-info-popover/site-info-popover.component";
import {CreateSiteDialogComponent} from "./create-site-dialog/create-site-dialog.component";
import { EditSiteDialogComponent } from './edit-site-dialog/edit-site-dialog.component';



@NgModule({
  declarations: [
    SiteAdminComponent,
    SiteListComponent,
    SiteInfoPopoverComponent,
    CreateSiteDialogComponent,
    EditSiteDialogComponent
  ],
  imports: [
    SharedModule,
    SiteAdminRoutingModule
  ],
  exports: []
})
export class SiteAdminModule { }
