import {Injectable} from '@angular/core';
import { HttpClient, HttpErrorResponse } from "@angular/common/http";
import {catchError, map, take} from "rxjs/operators";

import {Observable, of, ReplaySubject} from "rxjs";
import {AuthenticatedService} from "./authenticated.service";
import {Event, Router} from "@angular/router";
import {LoginModel} from "../../login/login-model";
import {environment} from "../../../environments/environment";
import {Role} from "./roles";
import {ActiveUser} from "./active-user";
import {EventsService} from "../events/events.service";
import {AEvent, EventType} from "../events/aevents";
import {ConfirmationService} from "primeng/api";
import {DialogService} from "primeng/dynamicdialog";


@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private authSubjet = new ReplaySubject<ActiveUser>();
  $auth$ = this.authSubjet.asObservable().pipe(
    take(1),
    map((active) => {
      if (active){
        return active;
      } else {
        return JSON.parse(<string>localStorage.getItem("activeUser"));
      }
    })
  ) as Observable<ActiveUser>;


  constructor(private httpClient: HttpClient, private authenticatedService: AuthenticatedService,private router: Router, private eventService: EventsService, private readonly :DialogService, private asd: ConfirmationService) { }

  async loginUser(loginModel$: Observable<LoginModel>)  {
    if (this.isMockedLoggin()){
      this.mockLogin();
    }
    await loginModel$.pipe(
      map(model => {
        this.httpClient.post<any>(this.backendUrl() + "login", this.createPayload(model))
          .pipe(
            map(response => {
              // Check if user is a competitor and show appropriate message
              if (response.roles && response.roles.includes('COMPETITOR')) {
                this.eventService.nyHändelse(EventType.Error, new AEvent(EventType.Error, "login.competitorNotAllowed"));
                return null;
              }

              localStorage.setItem('loggedInUser', JSON.stringify(response.token));
              this.authenticatedService.changeStatus(true);
              return response;
            }),
            catchError((error: HttpErrorResponse) => {
              this.eventService.nyHändelse(EventType.Error, new AEvent(EventType.Error, "Cannot sign in"));
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
    return true;
  }

  private setActiveUser(data: any): void {
   let values: Array<string> = data.roles;

    const activeUser = {
      name: data.givenname + " " + data.familyname,
      roles: values,
      id: data.id,
      startnumber: data.startnumber,
      trackuid: data.trackuid,
      organizer_id: data.organizer_id || data.organizerId
    } as ActiveUser
    localStorage.setItem('activeUser', JSON.stringify(activeUser));
    this.authSubjet.next(activeUser)
  }

  private redirect(roles: string[]) {
    // Handle multiple roles - check for highest priority role
    if (roles.includes(Role.ADMIN) || roles.includes(Role.SUPERUSER) || roles.includes(Role.USER)) {
      this.router.navigate(['admin/brevet-admin-start']);
    } else if (roles.includes(Role.VOLONTEER)) {
      this.router.navigate(['volunteer']);
    } else {
      // Default fallback
      this.router.navigate(['admin/brevet-admin-start']);
    }
  }

  private mockLogin(){
    localStorage.setItem('loggedInUser', 'fake_token');
    this.authenticatedService.changeStatus(true);
    const roles = ["ADMIN"];
    this.redirect(roles)
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
    this.authSubjet.next(null);
    this.authenticatedService.authenticatedSubject.next(false);
    this.authenticatedService.updateAuthenticationStatus();
    this.router.navigate(['/login']);
  }

  public reload(){
    this.authSubjet.next(null);
  }

  public validateToken(): Observable<boolean> {
    const token = localStorage.getItem('loggedInUser');

    if (!token) {
      return of(false);
    }

    return this.httpClient.get(this.backendUrl() + 'ping').pipe(
      map(() => true),
      catchError(() => {
        this.logoutUser();
        return of(false);
      })
    );
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
