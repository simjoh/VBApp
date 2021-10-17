import { NgModule } from '@angular/core';
import {SharedModule} from "../shared/shared.module";
import {LoginComponent} from "./login.component";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";



@NgModule({
  declarations: [LoginComponent],
  imports: [
    SharedModule
  ], exports: [LoginComponent]
})
export class LoginModule { }
