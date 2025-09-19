import { HttpInterceptorFn } from '@angular/common/http';

export const tokenInterceptor: HttpInterceptorFn = (req, next) => {
  const token = localStorage.getItem('loggedInUser');

  if (token) {
    const tokenReq = req.clone({
      setHeaders: {
        TOKEN: token
      }
    });
    return next(tokenReq);
  }

  return next(req);
};
