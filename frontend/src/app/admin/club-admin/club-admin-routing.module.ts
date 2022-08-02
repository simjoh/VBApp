import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {ClubAdminComponent} from "./club-admin.component";
import {ClubListComponent} from "./club-list/club-list.component";

const routes: Routes = [
  {
    path: '',
    component: ClubAdminComponent,
    children: [
      { path: 'brevet-club-list', component:  ClubListComponent},
      { path: '**', redirectTo: 'brevet-club-list'},
    ]
  }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ClubAdminRoutingModule { }
