import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { NgbdTableComplete } from './competitors-list/competitors-table-complete';
//import { KontrollerComponent } from './kontroller/kontroller.component';
//import { KontrollFormComponent } from './kontroll-form/kontroll-form.component';
import { KontrollerCombinerComponent } from './kontroller-combiner/kontroller-combiner.component';
import { MapComponent } from './map/map.component';

const routes: Routes = [
  { path: 'competitors-list-component', component: NgbdTableComplete },
  // { path: 'kontroller', component: KontrollerComponent },
  // { path: 'kontroll-form', component: KontrollFormComponent },
  { path: 'brevet-kontroller-combiner', component: KontrollerCombinerComponent },
  { path: 'brevet-map', component: MapComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
