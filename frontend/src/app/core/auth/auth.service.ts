import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {map} from "rxjs/operators";

import {BehaviorSubject, Observable, ReplaySubject, Subject} from "rxjs";
import {AuthenticatedService} from "./authenticated.service";
import {Router} from "@angular/router";
import {LoginModel} from "../../login/login-model";
import {environment} from "../../../environments/environment";
import {Role} from "./roles";
import {ActiveUser} from "./active-user";


@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private authSubjet = new ReplaySubject<ActiveUser>();
  $auth$ = this.authSubjet.asObservable();

  constructor(private httpClient: HttpClient, private authenticatedService: AuthenticatedService,private router: Router) { }

  async loginUser(loginModel$: Observable<LoginModel>)  {

    if (this.isMockedLoggin()){
      this.mockLogin();
    }
    await loginModel$.pipe(
      map(model => {
        this.httpClient.post<any>(this.backendUrl() + "/login", this.createPayload(model))
          .pipe(
            map(response => {
              console.log(response);
              localStorage.setItem('loggedInUser', JSON.stringify(response.token));
              this.authenticatedService.changeStatus(true);
              return response;
            })).toPromise().then((data) => {
          // where to go
            const role = data.role;
            this.setActiveUser()
            this.redirect(role);
        });
      })
    ).toPromise();
  }

  private setActiveUser(): void {
    const activeUser = {
      name: 'Test user'
    } as ActiveUser
    this.authSubjet.next(activeUser)
  }

  private  redirect(role: string): void{

    if ((role === Role.ADMIN|| role === Role.SUPERADMIN ||  role === Role.USER)) {
      this.router.navigate(['admin']);
    } else if (role === Role.COMPETITOR){
      this.router.navigate(['competitor']);
    } else if (role === Role.VOLONTEER) {
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
    this.authSubjet.next()
    this.authenticatedService.authenticatedSubject.next(false);
  }

  private backendUrl(): string{
    if (!environment.production && environment.mock_backend){
      if (environment.mockbackendurl != ''){
        return environment.mockbackendurl;
      } else {
        return environment.backend_url;
      }
    } else {
      return environment.backend_url;
    }
  }
}

export interface LoginPayload {
  username: any,
  password: any;
}
