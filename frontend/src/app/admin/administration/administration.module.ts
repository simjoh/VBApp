import {NgModule} from '@angular/core';

import {AdministrationRoutingModule} from './administration-routing.module';
import {AdministrationComponent} from "./administration.component";
import {SharedModule} from "../../shared/shared.module";


@NgModule({
  declarations: [
    AdministrationComponent
  ],
  imports: [
    SharedModule,
    AdministrationRoutingModule,

  ]
})
export class AdministrationModule {
}
