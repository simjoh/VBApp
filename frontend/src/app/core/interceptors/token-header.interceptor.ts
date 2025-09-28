import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class TokenHeaderInterceptor implements HttpInterceptor {

  constructor() {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    // Skip loppservice requests - they have their own authentication
    if (request.url.includes('/loppservice/')) {
      return next.handle(request);
    }

    let loggedInUser = "loggedInUser"
    let token = JSON.parse(<string>localStorage.getItem(loggedInUser));

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
