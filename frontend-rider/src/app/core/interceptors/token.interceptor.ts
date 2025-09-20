import { HttpInterceptorFn } from '@angular/common/http';

export const tokenInterceptor: HttpInterceptorFn = (req, next) => {
  const token = localStorage.getItem('riderToken');

  console.log(`[TokenInterceptor] Request to: ${req.url}`);
  console.log(`[TokenInterceptor] Token from localStorage:`, token ? `Present (${token.substring(0, 20)}...)` : 'Missing');

  if (token) {
    const tokenReq = req.clone({
      setHeaders: {
        TOKEN: token
      }
    });
    console.log(`[TokenInterceptor] Added TOKEN header to request`);
    return next(tokenReq);
  } else {
    console.log(`[TokenInterceptor] No token found, proceeding without TOKEN header`);
  }

  return next(req);
};
