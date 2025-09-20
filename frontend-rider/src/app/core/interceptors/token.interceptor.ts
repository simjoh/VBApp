import { HttpInterceptorFn } from '@angular/common/http';

export const tokenInterceptor: HttpInterceptorFn = (req, next) => {
  const token = localStorage.getItem('riderToken');

  // Token interceptor processing request

  if (token) {
    const tokenReq = req.clone({
      setHeaders: {
        TOKEN: token
      }
    });
    return next(tokenReq);
  } else {
    // No token found, proceeding without TOKEN header
  }

  return next(req);
};
