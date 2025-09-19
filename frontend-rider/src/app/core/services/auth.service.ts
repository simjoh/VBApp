import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { catchError, map, take, delay } from 'rxjs/operators';
import { environment } from '../../../environments/environment';

export interface LoginModel {
  username: string;
  password: string;
}

export interface LoginPayload {
  username: string;
  password: string;
}

export interface ActiveUser {
  name: string;
  roles: string[];
  id: string;
  startnumber?: string;
  trackuid?: string;
  organizer_id?: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private httpClient = inject(HttpClient);
  private authenticatedSubject = new BehaviorSubject<boolean>(this.isAuthenticated());
  authenticated$ = this.authenticatedSubject.asObservable();

  private isAuthenticated(): boolean {
    const token = localStorage.getItem("loggedInUser");
    return token !== null && token !== undefined && token !== 'null';
  }

  changeStatus(authenticated: boolean) {
    this.authenticatedSubject.next(authenticated);
  }

  updateAuthenticationStatus() {
    this.authenticatedSubject.next(this.isAuthenticated());
  }

  login(loginModel: LoginModel): Observable<boolean> {
    console.log('AuthService.login called with:', loginModel);

    if (this.isMockedLogin()) {
      console.log('Using mock login');
      this.mockLogin();
      return of(true);
    }

    const url = this.getBackendUrl() + "login";
    const payload = this.createPayload(loginModel);
    console.log('Making login request to:', url);
    console.log('With payload:', payload);

    return this.httpClient.post<any>(url, payload)
      .pipe(

        map(response => {
          console.log('Login response:', response);

          // Check if user has COMPETITOR role
          if (!response.roles || !response.roles.includes('COMPETITOR')) {
            console.log('User does not have COMPETITOR role. Roles:', response.roles);
            return false;
          }

          localStorage.setItem('loggedInUser', JSON.stringify(response.token));
          this.authenticatedSubject.next(true);
          this.setActiveUser(response);
          return true;
        }),
        catchError((error: HttpErrorResponse) => {
          console.error('Login error:', error);
          console.error('Error details:', error.error);
          return of(false);
        })
      );
  }

  private setActiveUser(data: any): void {
    const activeUser: ActiveUser = {
      name: data.givenname + " " + data.familyname,
      roles: data.roles,
      id: data.id,
      startnumber: data.startnumber,
      trackuid: data.trackuid,
      organizer_id: data.organizer_id || data.organizerId
    };

    localStorage.setItem('activeUser', JSON.stringify(activeUser));
  }

  private mockLogin(): void {
    localStorage.setItem('loggedInUser', 'fake_token');
    this.authenticatedSubject.next(true);
  }

  private createPayload(loginModel: LoginModel): LoginPayload {
    return {
      username: loginModel.username,
      password: loginModel.password
    };
  }

  private isMockedLogin(): boolean {
    return environment.production === false &&
           (environment as any).mock_login === true;
  }

  private getBackendUrl(): string {
    if (!environment.production && environment.mock_backend) {
      if (environment.mockbackendurl !== '') {
        return environment.mockbackendurl;
      } else {
        return environment.backend_url;
      }
    } else {
      return environment.backend_url;
    }
  }

  logout() {
    localStorage.removeItem('loggedInUser');
    localStorage.removeItem('activeUser');
    this.changeStatus(false);
  }
}
