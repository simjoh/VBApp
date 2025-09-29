import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { Observable } from 'rxjs';

@Injectable()
export class LoppserviceApiKeyInterceptor implements HttpInterceptor {

  constructor() {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    // Only apply to loppservice requests
    if (!request.url.includes('/loppservice/')) {
      return next.handle(request);
    }

    // Add API key for loppservice
    request = request.clone({
      setHeaders: {
        'APIKEY': `${environment.loppservice_api_key}`
      }
    });

    return next.handle(request);
  }
}

