import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {EventAdminComponent} from "./event-admin.component";
import {EventListComponent} from "./event-list/event-list.component";

const routes: Routes = [
  {
    path: '',
    component: EventAdminComponent,
    children: [
      { path: 'brevet-event-list', component:  EventListComponent},
      { path: '', redirectTo: 'brevet-event-list', pathMatch: 'full'},
    ]
  }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EventAdminRoutingModule { }
