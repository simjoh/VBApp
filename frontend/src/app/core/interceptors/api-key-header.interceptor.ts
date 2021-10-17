import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor
} from '@angular/common/http';
import {environment} from "../../../environments/environment";
import { Observable } from 'rxjs';

@Injectable()
export class ApiKeyHeaderInterceptor implements HttpInterceptor {

  constructor() {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    request = request.clone({
      setHeaders: {
        'API_KEY': `${environment.api_key}`
      }
    });
    return next.handle(request);
  }
}
