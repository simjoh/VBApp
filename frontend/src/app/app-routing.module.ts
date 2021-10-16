import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { NgbdTableComplete } from './competitors-list/competitors-table-complete';

const routes: Routes = [
  { path: 'competitors-list-component', component: NgbdTableComplete },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
