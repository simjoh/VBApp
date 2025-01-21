import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {SiteAdminComponent} from "../site-admin/site-admin.component";
import {SiteListComponent} from "../site-admin/site-list/site-list.component";
import {OrganizerAdminComponent} from "./organizer-admin.component";
import {OrganizerListComponent} from "./organizer-list/organizer-list.component";


const routes: Routes = [
  {
    path: 'organizers',
    component: OrganizerAdminComponent,
    children: [
      {path: 'brevet-organizer-list', component: OrganizerListComponent},
      {path: '**', redirectTo: 'brevet-organizer-list'},
    ]
  }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OrganizerAdminRoutingModule {
}
