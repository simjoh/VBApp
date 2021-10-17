import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor
} from '@angular/common/http';
import { Observable } from 'rxjs';
import {finalize} from "rxjs/operators";
import {PendingRequestsService} from "../pending-requests.service";

@Injectable()
export class PendingRequestInterceptor implements HttpInterceptor {

  constructor(private pendingRequestsService: PendingRequestsService) {
  }

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    this.pendingRequestsService.increase();
    return next.handle(request).pipe(finalize(() => {
      this.pendingRequestsService.decrease();
    }));
  }
}
