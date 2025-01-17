import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AdminComponent} from "./admin.component";
import {AuthenticatedGuard} from "../core/auth/authenticated.guard";
import {AdminStartComponent} from "./admin-start/admin-start.component";

const routes: Routes = [{
  path: 'admin',
  component: AdminComponent,
  canActivate: [AuthenticatedGuard],
  children: [
    {
      path: 'participant',
      loadChildren: () => import('./participant/participant.module').then(m => m.ParticipantModule),
    },
    {
      path: 'banor',
      loadChildren: () => import('./track/track.module').then(m => m.TrackModule),
    },
    {
      path: 'administration',
      loadChildren: () => import('./administration/administration.module').then(m => m.AdministrationModule),
    },
    {
      path: 'useradmin',
      loadChildren: () => import('./user-admin/user-admin.module').then(m => m.UserAdminModule),
    },
    {
      path: 'clubadmin',
      loadChildren: () => import('./club-admin/club-admin.module').then(m => m.ClubAdminModule),
    },
    {
      path: 'siteadmin',
      loadChildren: () => import('./site-admin/site-admin.module').then(m => m.SiteAdminModule),
    },
    {
      path: 'eventadmin',
      loadChildren: () => import('./event-admin/event-admin.module').then(m => m.EventAdminModule),
    },
    { path: 'brevet-admin-start', component: AdminStartComponent },
  ]
}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdminRoutingModule { }
