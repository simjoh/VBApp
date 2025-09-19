import { ApplicationConfig, provideBrowserGlobalErrorListeners, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { provideAnimations } from '@angular/platform-browser/animations';

import { routes } from './app.routes';
import { apiKeyInterceptor } from './core/interceptors/api-key.interceptor';
import { tokenInterceptor } from './core/interceptors/token.interceptor';
import { authInterceptor } from './core/interceptors/auth.interceptor';
import { unauthorizedInterceptor } from './core/interceptors/unauthorized.interceptor';
import { pendingRequestsInterceptor } from './core/interceptors/pending-requests.interceptor';
import { feedbackInterceptor } from './core/interceptors/feedback.interceptor';

export const appConfig: ApplicationConfig = {
  providers: [
    provideBrowserGlobalErrorListeners(),
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
    provideHttpClient(
      withInterceptors([
        apiKeyInterceptor,
        tokenInterceptor,
        authInterceptor,
        unauthorizedInterceptor,
        pendingRequestsInterceptor,
        feedbackInterceptor
      ])
    ),
    provideAnimations()
  ]
};
