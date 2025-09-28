import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from "rxjs/operators";
import { AuthService } from "../auth/auth.service";

@Injectable()
export class NotauthorizedInterceptor implements HttpInterceptor {

  constructor(private authService: AuthService) {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(
      catchError((error: HttpErrorResponse) => {
        const errorCodes = [401, 403, 405];
        // Don't log out for loppservice API errors - they have their own authentication
        if (errorCodes.includes(error.status) && !request.url.includes('/loppservice/')) {
          this.authService.logoutUser();
        }
        return throwError(() => error);
      })
    );
  }
}
