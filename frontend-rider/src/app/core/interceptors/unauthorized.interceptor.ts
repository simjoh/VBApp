import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';

export const unauthorizedInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);

  return next(req).pipe(
    catchError((error) => {
      const errorCodes = [401, 403, 405];
      if (errorCodes.includes(error.status)) {
        // Clear authentication data
        localStorage.removeItem('loggedInUser');
        localStorage.removeItem('activeUser');
        // Redirect to login or home
        router.navigate(['/']);
      }
      return throwError(() => error);
    })
  );
};
