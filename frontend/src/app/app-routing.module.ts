import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { CompetitorsListComponent } from './competitors-list/competitors-list.component';

const routes: Routes = [
  { path: 'competitors-list-component', component: CompetitorsListComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
