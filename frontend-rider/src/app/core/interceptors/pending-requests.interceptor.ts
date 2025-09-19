import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { finalize } from 'rxjs/operators';
import { PendingRequestsService } from '../services/pending-requests.service';

export const pendingRequestsInterceptor: HttpInterceptorFn = (req, next) => {
  const pendingRequestsService = inject(PendingRequestsService);

  pendingRequestsService.increase();

  return next(req).pipe(
    finalize(() => {
      pendingRequestsService.decrease();
    })
  );
};
