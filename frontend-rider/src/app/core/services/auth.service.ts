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

          // Check if user has COMPETITOR role (allow ADMIN for testing)
          if (!response.roles || (!response.roles.includes('COMPETITOR') && !response.roles.includes('ADMIN'))) {
            console.log('User does not have COMPETITOR or ADMIN role. Roles:', response.roles);
            return false;
          }

          console.log('Storing token:', response.token);
          localStorage.setItem('loggedInUser', response.token);
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
    console.log('Setting active user with data:', data);

    // Handle different possible name formats from backend
    let userName = 'Unknown User';
    if (data.givenname && data.familyname) {
      userName = data.givenname + " " + data.familyname;
    } else if (data.name) {
      userName = data.name;
    } else if (data.username) {
      userName = data.username;
    } else if (data.email) {
      userName = data.email;
    } else if (data.startnumber) {
      // For competitor users, use startnumber as display name
      userName = `Rider #${data.startnumber}`;
    }

    // Use the actual data from the backend response
    let startnumber = data.startnumber;
    let trackuid = data.trackuid;

    console.log('User data from backend:', {
      startnumber: startnumber,
      trackuid: trackuid,
      roles: data.roles
    });

    const activeUser: ActiveUser = {
      name: userName,
      roles: data.roles || [],
      id: data.id || data.uid || 'unknown',
      startnumber: startnumber,
      trackuid: trackuid,
      organizer_id: data.organizer_id || data.organizerId
    };

    console.log('Created active user object:', activeUser);
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

  getActiveUser(): ActiveUser | null {
    const activeUserData = localStorage.getItem('activeUser');
    if (activeUserData) {
      try {
        return JSON.parse(activeUserData) as ActiveUser;
      } catch (error) {
        console.error('Error parsing active user data:', error);
        return null;
      }
    }
    return null;
  }
}
