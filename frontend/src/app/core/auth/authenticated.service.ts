import { Injectable } from '@angular/core';
import {BehaviorSubject} from "rxjs";
import {map} from "rxjs/operators";

@Injectable({
  providedIn: 'root'
})
export class AuthenticatedService {

  authenticatedSubject = new BehaviorSubject<boolean>(false);
  authenticated$ = this.authenticatedSubject.asObservable().pipe(
    map(() => {
      return localStorage.getItem("loggedInUser") != null
    })
  );

  changeStatus(authenticated: boolean){
    this.authenticatedSubject.next(authenticated);
  }
}
