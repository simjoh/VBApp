import {NgModule, Optional, SkipSelf} from '@angular/core';
import { CommonModule } from '@angular/common';
import { MenuComponent } from './menu/menu.component';
import {SharedModule} from "../shared/shared.module";
import {RouterModule} from "@angular/router";
import {CardModule} from "primeng/card";
import {AppModule} from "../app.module";



@NgModule({
  declarations: [
    MenuComponent
  ],
    imports: [
        RouterModule,
        CommonModule,
        SharedModule,
    ],
  exports: [MenuComponent],
  providers: [
    {provide: Window, useValue: window},
  ],
})
export class CoreModule {

  constructor(@Optional() @SkipSelf() parentModule: CoreModule) {
    this.throwIfAlreadyLoaded(parentModule, 'CoreModule');
  }

  throwIfAlreadyLoaded(parentModule: any, moduleName: string) {
    if (parentModule) {
      throw new Error(`${moduleName} has already been loaded. Import Core modules in the AppModule only.`);
    }
  }

}
