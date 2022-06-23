import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor, HttpErrorResponse
} from '@angular/common/http';
import { Observable } from 'rxjs';
import {catchError, tap} from "rxjs/operators";
import {AuthService} from "../auth/auth.service";


@Injectable()
export class NotauthorizedInterceptor implements HttpInterceptor {

  constructor(private authService: AuthService) {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(
      tap((event) => {
        return event;
      }, (error: any) => {
        if (error instanceof HttpErrorResponse) {
          const errorcode = [401, 403, 405];
          if (errorcode.some(felkod => felkod === error.status)) {
            this.authService.logoutUser()
          }
        }
      })
    );
  }
}
