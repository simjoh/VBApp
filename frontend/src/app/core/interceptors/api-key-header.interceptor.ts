import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import {environment} from "../../../environments/environment";
import { Observable } from 'rxjs';

@Injectable()
export class ApiKeyHeaderInterceptor implements HttpInterceptor {

  constructor() {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    // Skip loppservice requests - they have their own API key
    if (request.url.includes('/loppservice/')) {
      return next.handle(request);
    }

    request = request.clone({
      setHeaders: {
        'APIKEY': `${environment.api_key}`
      }
    });
    return next.handle(request);
  }
}
