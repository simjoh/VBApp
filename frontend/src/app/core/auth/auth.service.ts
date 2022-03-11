import {Injectable} from '@angular/core';
import {HttpClient, HttpErrorResponse} from "@angular/common/http";
import {catchError, map} from "rxjs/operators";

import {Observable, of, ReplaySubject} from "rxjs";
import {AuthenticatedService} from "./authenticated.service";
import {Event, Router} from "@angular/router";
import {LoginModel} from "../../login/login-model";
import {environment} from "../../../environments/environment";
import {Role} from "./roles";
import {ActiveUser} from "./active-user";
import {EventsService} from "../events/events.service";
import {AEvent, EventType} from "../events/aevents";


@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private authSubjet = new ReplaySubject<ActiveUser>();
  $auth$ = this.authSubjet.asObservable().pipe(
    map((active) => {
      if (active){
        return active;
      } else {
        return JSON.parse(<string>localStorage.getItem("activeUser"));
      }
    })
  ) as Observable<ActiveUser>;


  constructor(private httpClient: HttpClient, private authenticatedService: AuthenticatedService,private router: Router, private eventService: EventsService) { }

  async loginUser(loginModel$: Observable<LoginModel>)  {

    if (this.isMockedLoggin()){
      this.mockLogin();
    }
    await loginModel$.pipe(
      map(model => {
        this.httpClient.post<any>(this.backendUrl() + "login", this.createPayload(model))
          .pipe(
            map(response => {
              console.log(response);
              localStorage.setItem('loggedInUser', JSON.stringify(response.token));
              this.authenticatedService.changeStatus(true);
              return response;
            }),
            catchError((error: HttpErrorResponse) => {
              this.eventService.nyHändelse(EventType.Error, new AEvent(EventType.Error, "Unable to login"));
              return of(null);
            })
            ).toPromise().then((data) => {
              if (data){
                this.setActiveUser(data)
                this.redirect(data.roles);
              } else {
                this.logoutUser();
              }

        });
      })
    ).toPromise();
  }

  private setActiveUser(data: any): void {
   let values: Array<string> = data.roles;

    const activeUser = {
      name: data.givenname + " " + data.familyname,
      roles: values,
      startnumber: data.startnumber,
      trackuid: data.trackuid
    } as ActiveUser
    localStorage.setItem('activeUser', JSON.stringify(activeUser));
    this.authSubjet.next(activeUser)
  }

  private redirect(roles: string): void{
    let role = null;
    if (roles.length === 1){
      role = roles[0];
    }
    if ((role === Role.ADMIN|| role === Role.SUPERUSER ||  role === Role.USER)) {
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
    localStorage.removeItem('activeUser');
    this.authSubjet.next()
    this.authenticatedService.authenticatedSubject.next(false);
  }

  public reload(){
    this.authSubjet.next();
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
