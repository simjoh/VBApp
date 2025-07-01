import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {UserAdminComponent} from "./user-admin.component";
import {UserListComponent} from "./user-list/user-list.component";
import {PermissionAdminComponent} from "./permission-admin/permission-admin.component";

const routes: Routes = [
  {
    path: '',
    component: UserAdminComponent,
    children: [
      { path: 'brevet-user-list', component:  UserListComponent},
      { path: 'brevet-permissions', component:  PermissionAdminComponent},
      { path: '', redirectTo: 'brevet-user-list', pathMatch: 'full'},
    ]
  }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UserAdminRoutingModule { }
