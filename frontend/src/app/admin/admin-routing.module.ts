import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AdminComponent} from "./admin.component";
import {AuthenticatedGuard} from "../core/auth/authenticated.guard";
import {NgbdTableComplete} from "./competitors-list/competitors-table-complete";
import {KontrollerCombinerComponent} from "./kontroller-combiner/kontroller-combiner.component";

const routes: Routes = [{
  path: 'admin',
  component: AdminComponent,
  canActivate: [AuthenticatedGuard],
},
  {
    path: '',
    redirectTo: '',
    pathMatch: 'full'
  },
  { path: 'brevet-kontroller-combiner', component: KontrollerCombinerComponent },
  { path: 'competitors-list-component', component: NgbdTableComplete },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdminRoutingModule { }
