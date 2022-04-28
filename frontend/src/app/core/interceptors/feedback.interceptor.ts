import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor, HttpErrorResponse
} from '@angular/common/http';
import {catchError} from "rxjs/operators";
import {Observable, throwError as observableThrowError} from 'rxjs';
import {MessageService} from "primeng/api";

@Injectable()
export class FeedbackInterceptor implements HttpInterceptor {

  constructor(private messageService: MessageService) {}

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(
      catchError(err => {
        if (err instanceof HttpErrorResponse) {
          if (Number(err.error.code) >= 6 ){
            this.messageService.add({key: 'tc', severity:'warn', summary: 'Error', detail: this.felmeddelandeFor(err)});
          } else {
            this.messageService.add({key: 'tc', severity:'error', summary: 'Error', detail: this.felmeddelandeFor(err)});
          }

        }
        return observableThrowError(err);
      })) as Observable<any>;
  }


  private felmeddelandeFor(response: HttpErrorResponse) {

    if (Number(response.error.code) >= 6 ){
      return response.error.message
    }


    switch (response.status) {
      case 502:
        return 'Service unavailable';
      case 504:
        // Klura på meddelande för gateway timeout
        return 'Service unavailable';
      default:
        return 'An error occured. Contact administrator if the problem persists ';
    }
  }
}
