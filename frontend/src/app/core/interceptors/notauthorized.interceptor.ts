import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor, HttpErrorResponse
} from '@angular/common/http';
import { Observable } from 'rxjs';
import {tap} from "rxjs/operators";
import {AuthService} from "../auth/auth.service";

@Injectable()
export class NotauthorizedInterceptor implements HttpInterceptor {

  constructor(private authService: AuthService) {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    return next.handle(request).pipe(
      tap(event => {
        return event;
      }, error => {
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
