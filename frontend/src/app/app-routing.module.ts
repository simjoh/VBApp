import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {UnknownRouteComponent} from "./unknown-route/unknown-route.component";
import {AuthenticatedGuard} from "./core/auth/authenticated.guard";
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
  }
  // { path: 'kontroller', component: KontrollerComponent },
  // { path: 'kontroll-form', component: KontrollFormComponent },
];
@NgModule({
  imports: [RouterModule.forRoot(ROUTES)],
  exports: [RouterModule]
})
export class AppRoutingModule { }

