import { Injectable } from '@angular/core';
import { BehaviorSubject } from "rxjs";
import { map } from "rxjs/operators";

@Injectable({
  providedIn: 'root'
})
export class AuthenticatedService {

  authenticatedSubject = new BehaviorSubject<boolean>(this.isAuthenticated());
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
}
