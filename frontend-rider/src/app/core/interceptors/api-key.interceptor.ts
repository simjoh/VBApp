import { HttpInterceptorFn } from '@angular/common/http';
import { environment } from '../../../environments/environment';

export const apiKeyInterceptor: HttpInterceptorFn = (req, next) => {
  const apiKeyReq = req.clone({
    setHeaders: {
      APIKEY: environment.api_key
    }
  });

  return next(apiKeyReq);
};
