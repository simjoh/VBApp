import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError } from 'rxjs/operators';
import { throwError } from 'rxjs';
import { MessageService } from '../services/message.service';

export const feedbackInterceptor: HttpInterceptorFn = (req, next) => {
  const messageService = inject(MessageService);

  // Allow developers to suppress error toasts per-request by adding header:
  //  - 'X-Ignore-Errors': 'true' (or 'X-Suppress-Error': 'true')
  const shouldSuppress = req.headers.get('X-Ignore-Errors') === 'true' || req.headers.get('X-Suppress-Error') === 'true';

  // Do not pass the internal header to the backend
  let sanitizedHeaders = req.headers;
  if (sanitizedHeaders.has('X-Ignore-Errors')) {
    sanitizedHeaders = sanitizedHeaders.delete('X-Ignore-Errors');
  }
  if (sanitizedHeaders.has('X-Suppress-Error')) {
    sanitizedHeaders = sanitizedHeaders.delete('X-Suppress-Error');
  }
  const sanitizedRequest = (sanitizedHeaders === req.headers) ? req : req.clone({ headers: sanitizedHeaders });

  return next(sanitizedRequest).pipe(
    catchError(err => {
      if (!shouldSuppress && err.error) {
        const errorCode = Number(err.error.code);

        if (errorCode >= 6) {
          if (errorCode === 7) {
            messageService.showInfo('Info', felmeddelandeFor(err));
          } else {
            messageService.showWarning('Warning', felmeddelandeFor(err));
          }
        } else {
          messageService.showError('Error', felmeddelandeFor(err));
        }
      }
      return throwError(() => err);
    })
  );
};

function felmeddelandeFor(response: any): string {
  if (Number(response.error?.code) >= 6) {
    return response.error.message;
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
