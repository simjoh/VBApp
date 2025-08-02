import { NgModule } from '@angular/core';
import {SharedModule} from "../shared/shared.module";
import {LoginComponent} from "./login.component";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {LoginRoutingModule} from "./login-routing.module";

@NgModule({
  declarations: [LoginComponent],
  imports: [
    SharedModule,
    LoginRoutingModule
  ], 
  exports: [LoginComponent]
})
export class LoginModule { }
