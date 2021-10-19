import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {map} from "rxjs/operators";

import {BehaviorSubject, Observable} from "rxjs";
import {AuthenticatedService} from "./authenticated.service";
import {Router} from "@angular/router";
import {LoginModel} from "../../login/login-model";
import {environment} from "../../../environments/environment";
import {Roles} from "./roles";

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
            this.redirect(role);
        });
      })
    ).toPromise();
  }

  private  redirect(role: string): void{
    if (role === Roles.ADMIN || Roles.SUPERADMIN || Roles.USER) {
      this.router.navigate(['admin']);
    } else if (role === Roles.COMPETITOR){
      this.router.navigate(['competitor']);
    } else if (role === Roles.VOLONTEER) {
      this.router.navigate(['volunteer']);
    }
  }

  private mockLogin(){
    localStorage.setItem('loggedInUser', 'fake_token');
    this.authenticatedService.changeStatus(true);
    const role = "ADMIN";
      this.redirect(role)
  }

  private createPayload(loginmodel: LoginModel): LoginPayload{
    return {
      username: loginmodel.username,
      password: loginmodel.password
    } as LoginPayload;
  }

  private isMockedLoggin(): boolean{
    return environment.mock_login;
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
