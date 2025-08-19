import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {UnknownRouteComponent} from "./unknown-route/unknown-route.component";
import {AuthenticatedGuard} from "./core/auth/authenticated.guard";
import {NgbdTableComplete} from "./admin/competitors-list/competitors-table-complete";
import { KontrollFormComponent } from './admin/kontroll-form/kontroll-form.component';

export const ROUTES: Routes = [
  {
    path: 'login',
    loadChildren: () => import('./login/login.module').then(m => m.LoginModule),
  },
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
        path: 'volunteer',
        canActivate: [AuthenticatedGuard],
        loadChildren: () => import('./volunteer/volunteer.module').then(m => m.VolunteerModule),
      },
      {
        path: '',
        redirectTo: '/admin/brevet-admin-start',
        pathMatch: 'full'
      },
      {
        path: '**',
        pathMatch: "full",
        component: UnknownRouteComponent
      }
    ],
  },
  // { path: 'kontroller', component: KontrollerComponent },
  { path: 'kontroll-form', component: KontrollFormComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(ROUTES)],
  exports: [RouterModule]
})
export class AppRoutingModule { }

