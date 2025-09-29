import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class LoppserviceTokenInterceptor implements HttpInterceptor {

  constructor() {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    // Only apply to loppservice requests
    if (!request.url.includes('/loppservice/')) {
      return next.handle(request);
    }

    // Get JWT token from localStorage
    const loggedInUser = "loggedInUser";
    const token = JSON.parse(<string>localStorage.getItem(loggedInUser));

    if (token) {
      request = request.clone({
        setHeaders: {
          TOKEN: `${token}`
        }
      });
    }

    return next.handle(request);
  }
}

