import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {CompetitorComponent} from "../competitor/competitor.component";
import {AuthenticatedGuard} from "../core/auth/authenticated.guard";
import {VolunteerComponent} from "./volunteer/volunteer.component";
import {ListComponent} from "../competitor/list/list.component";

const routes: Routes = [{
  path: 'volunteer',
  component: VolunteerComponent,
  canActivate: [AuthenticatedGuard],
},
  {
    path: '',
    redirectTo: '',
    pathMatch: 'full'
  },
   // { path: 'brevet-list', component: ListComponent }
  // { path: 'brevet-maps', component: MapComponent },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VolunteerRoutingModule { }
