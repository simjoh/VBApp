import { ApplicationConfig, provideZoneChangeDetection } from '@angular/core';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';



import { providePrimeNG } from 'primeng/config';
import MyTheme from "./design/myTheme";


export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideAnimationsAsync(),
    providePrimeNG({
      theme: {
        preset: MyTheme
      }
    })
  ]
};
