import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {UnknownRouteComponent} from "./unknown-route/unknown-route.component";
import {AuthenticatedGuard} from "./core/auth/authenticated.guard";
import {NgbdTableComplete} from "./competitors-list/competitors-table-complete";
import {KontrollerCombinerComponent} from "./kontroller-combiner/kontroller-combiner.component";

export const ROUTES: Routes = [
  {
    path: '',
    canActivate: [AuthenticatedGuard],
    canActivateChild: [AuthenticatedGuard],
    children: [
      {
        path: 'admin',
        canActivate: [AuthenticatedGuard],
        loadChildren: () => import('./admin/admin.module').then(m => m.AdminModule),
      },
      {
        path: 'competitor',
        canActivate: [AuthenticatedGuard],
        loadChildren: () => import('./competitor/competitor.module').then(m => m.CompetitorModule),
      },
      {
        path: '',
        redirectTo: '',
        pathMatch: 'full'
      },
      {
        path: '**',
        pathMatch: "full",
        component: UnknownRouteComponent
      }
    ]
  },
  { path: 'competitors-list-component', component: NgbdTableComplete },
  // { path: 'kontroller', component: KontrollerComponent },
  // { path: 'kontroll-form', component: KontrollFormComponent },
  { path: 'brevet-kontroller-combiner', component: KontrollerCombinerComponent },
];
@NgModule({
  imports: [RouterModule.forRoot(ROUTES)],
  exports: [RouterModule]
})
export class AppRoutingModule { }

