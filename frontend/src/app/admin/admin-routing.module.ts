import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AdminComponent} from "./admin.component";
import {AuthenticatedGuard} from "../core/auth/authenticated.guard";

const routes: Routes = [{
  path: 'admin',
  component: AdminComponent,
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
export class AdminRoutingModule { }
