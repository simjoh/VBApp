import { NgModule } from '@angular/core';
import {SharedModule} from "../shared/shared.module";
import {LoginComponent} from "./login.component";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {LoginRoutingModule} from "./login-routing.module";
import {DisplayTranslationPipe} from "../shared/pipes/display-translation.pipe";

@NgModule({
  declarations: [LoginComponent],
  imports: [
    SharedModule,
    LoginRoutingModule,
    DisplayTranslationPipe
  ],
  exports: [LoginComponent]
})
export class LoginModule { }
