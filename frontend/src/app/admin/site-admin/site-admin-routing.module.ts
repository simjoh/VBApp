import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SiteAdminComponent } from './site-admin.component';
import { SiteListComponent } from './site-list/site-list.component';


const routes: Routes = [
  {
    path: '',
    component: SiteAdminComponent,
    children: [
      { path: 'brevet-site-list', component:  SiteListComponent},
      { path: '', redirectTo: 'brevet-site-list', pathMatch: 'full'},
    ]
  }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SiteAdminRoutingModule { }
