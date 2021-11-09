import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor
} from '@angular/common/http';
import {asapScheduler, BehaviorSubject, Observable} from 'rxjs';
import {finalize, observeOn} from "rxjs/operators";
import {PendingRequestsService} from "../pending-requests.service";

@Injectable()
export class PendingRequestInterceptor implements HttpInterceptor {

  countSubject = new BehaviorSubject(0);
  count$ = this.countSubject.asObservable().pipe(observeOn(asapScheduler));

  constructor(private pendingRequestsService: PendingRequestsService) {
  }

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    this.pendingRequestsService.increase();
    return next.handle(request).pipe(finalize(() => {
      this.pendingRequestsService.decrease();
    }));
  }
}
