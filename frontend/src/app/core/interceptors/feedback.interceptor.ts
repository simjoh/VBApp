import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor, HttpErrorResponse
} from '@angular/common/http';
import {catchError} from "rxjs/operators";
import {Observable, throwError as observableThrowError} from 'rxjs';

@Injectable()
export class FeedbackInterceptor implements HttpInterceptor {

  constructor() {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(
      catchError(err => {
        if (err instanceof HttpErrorResponse) {

        }
        return observableThrowError(err);
      })) as Observable<any>;
  }
}
