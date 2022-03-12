import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AdminComponent} from "../admin/admin.component";
import {AuthenticatedGuard} from "../core/auth/authenticated.guard";
import {CompetitorComponent} from "./competitor.component";
import {MapComponent} from "./map/map.component";
import {ListComponent} from "./list/list.component";
import {HasAccessToCompetitorFunctionsGuard} from "../core/auth/has-access-to-competitor-functions.guard";

const routes: Routes = [{
  path: 'competitor',
  component: CompetitorComponent,
  canActivate: [AuthenticatedGuard, HasAccessToCompetitorFunctionsGuard],
},
  {
    path: '',
    redirectTo: '',
    pathMatch: 'full'
  },
  { path: 'brevet-list', component: ListComponent,  canActivate: [AuthenticatedGuard, HasAccessToCompetitorFunctionsGuard] },
  { path: 'brevet-maps', component: MapComponent,  canActivate: [AuthenticatedGuard, HasAccessToCompetitorFunctionsGuard] },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CompetitorRoutingModule { }
