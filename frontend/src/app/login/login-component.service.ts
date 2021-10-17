import { Injectable } from '@angular/core';
import {ReplaySubject} from "rxjs";
import {LoginModel} from "./login-model";

@Injectable()
export class LoginComponentService {


  private loginModelSubject = new ReplaySubject<LoginModel>(1);
  loginModel$ = this.loginModelSubject.asObservable();

  initiateModel(): void {
    this.handleModel(new LoginModel());
  }
  private handleModel(loginModel: LoginModel) {
    this.loginModelSubject.next(loginModel);
  }
}
