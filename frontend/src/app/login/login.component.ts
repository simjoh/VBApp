import {Component, OnInit, ChangeDetectionStrategy, ViewChild, AfterViewInit} from '@angular/core';
import {AuthService} from "../core/auth/auth.service";
import {LoginComponentService} from "./login-component.service";
import {NgForm} from "@angular/forms";

@Component({
  selector: 'brevet-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
  providers: [LoginComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class LoginComponent implements  OnInit{

  @ViewChild("form") form: NgForm
  loginModel$ = this.loginComponetService.loginModel$

  constructor(private authServiceService: AuthService, private loginComponetService: LoginComponentService) {
  }

  login(){
    // @ts-ignore
    if (!this.form.invalid){
      this.authServiceService.loginUser(this.loginComponetService.loginModel$);
    }


  }


  ngOnInit(): void {
    this.loginComponetService.initiateModel();
  }


}



