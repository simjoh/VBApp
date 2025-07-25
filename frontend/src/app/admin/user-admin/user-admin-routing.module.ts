import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { UserAdminComponent } from "./user-admin.component";
import { PermissionAdminComponent } from "./permission-admin/permission-admin.component";

const routes: Routes = [
  {
    path: '',
    component: UserAdminComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UserAdminRoutingModule { }
