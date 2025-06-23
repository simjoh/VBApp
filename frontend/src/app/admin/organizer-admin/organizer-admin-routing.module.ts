import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { OrganizerAdminComponent } from './organizer-admin.component';
import { OrganizerListComponent } from './organizer-list/organizer-list.component';

const routes: Routes = [
  {
    path: '',
    component: OrganizerAdminComponent,
    children: [
      {
        path: 'brevet-organizer-list',
        component: OrganizerListComponent
      },
      {
        path: '',
        redirectTo: 'brevet-organizer-list',
        pathMatch: 'full'
      }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OrganizerAdminRoutingModule { }
