import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {map, mergeMap, take} from "rxjs/operators";

import {BehaviorSubject, Observable} from "rxjs";
import {AuthenticatedService} from "./authenticated.service";
import {Router} from "@angular/router";
import {LoginModel} from "../../login/login-model";
import {environment} from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private authSubjet = new BehaviorSubject<boolean>(false)
  $auth$ = this.authSubjet.asObservable();

  constructor(private httpClient: HttpClient, private authenticatedService: AuthenticatedService,private router: Router) { }

  async loginUser(loginModel$: Observable<LoginModel>)  {

    if (this.isMockedLoggin()){
      this.mockLogin();
    }
    await loginModel$.pipe(
      map(model => {
        this.httpClient.post<any>("/api/login", this.createPayload(model))
          .pipe(
            map(response => {
              console.log(response);
              localStorage.setItem('loggedInUser', JSON.stringify(response.token));
              this.authenticatedService.changeStatus(true);
              return response;
            })).toPromise().then((data) => {
          // where to go
           const role = "ADMIN";
          if (role !== role) {
            this.router.navigate(['admin']);
          } else {
            this.router.navigate(['competitor']);
          }
        });
      })
    ).toPromise();
  }

  private mockLogin(){
    localStorage.setItem('loggedInUser', 'fake_token');
    this.authenticatedService.changeStatus(true);
    const role = "ADMIN";
    if (role === role) {
      this.router.navigate(['admin']);
    } else {
      this.router.navigate(['competitor']);
    }
  }

  private createPayload(loginmodel: LoginModel): LoginPayload{
    return {
      username: loginmodel.username,
      password: loginmodel.password
    } as LoginPayload;
  }

  private isMockedLoggin(): boolean{
    return environment.mock_login
  }

  public logoutUser() {
    localStorage.removeItem('loggedInUser');
    this.authenticatedService.authenticatedSubject.next(false);
  }
}

export interface LoginPayload {
  username: any,
  password: any;
}
