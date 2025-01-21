import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdministrationRoutingModule } from './administration-routing.module';
import {AdministrationComponent} from "./administration.component";
import {TabMenuModule} from "primeng/tabmenu";


@NgModule({
  declarations: [
    AdministrationComponent
  ],
    imports: [
        CommonModule,
        AdministrationRoutingModule,
        TabMenuModule
    ]
})
export class AdministrationModule { }
