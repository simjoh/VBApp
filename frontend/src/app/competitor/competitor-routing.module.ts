import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AdminComponent} from "../admin/admin.component";
import {AuthenticatedGuard} from "../core/auth/authenticated.guard";
import {CompetitorComponent} from "./competitor.component";

const routes: Routes = [{
  path: 'competitor',
  component: CompetitorComponent,
  canActivate: [AuthenticatedGuard],
},
  {
    path: '',
    redirectTo: '',
    pathMatch: 'full'
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CompetitorRoutingModule { }
