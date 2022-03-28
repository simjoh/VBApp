import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {AdminComponent} from "./admin.component";
import {AuthenticatedGuard} from "../core/auth/authenticated.guard";
import {NgbdTableComplete} from "./competitors-list/competitors-table-complete";
import {KontrollerCombinerComponent} from "./kontroller-combiner/kontroller-combiner.component";
import {UserAdminComponent} from "./user-admin/user-admin.component";
import {DashboardComponent} from "./dashboard/dashboard.component";
import {SiteAdminComponent} from "./site-admin/site-admin.component";
import { EventAdminComponent } from './event-admin/event-admin.component';
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
    { path: 'brevet-admin-start', component: AdminStartComponent },
    { path: 'brevet-kontroller-combiner', component: KontrollerCombinerComponent},
    { path: 'brevet-user-admin', component: UserAdminComponent },
    { path: 'brevet-dashboard', component: DashboardComponent },
    { path: 'competitors-list-component', component: NgbdTableComplete },
    { path: 'brevet-site-admin', component: SiteAdminComponent},
    { path: 'brevet-event-admin', component: EventAdminComponent},
    { path: '**', redirectTo: 'brevet-admin-start'},
  ]
}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdminRoutingModule { }
