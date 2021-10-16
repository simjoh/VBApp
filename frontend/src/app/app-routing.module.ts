import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { NgbdTableComplete } from './competitors-list/competitors-table-complete';
import { KontrollerComponent } from './kontroller/kontroller.component';

const routes: Routes = [
  { path: 'competitors-list-component', component: NgbdTableComplete },
  { path: 'kontroller', component: KontrollerComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
