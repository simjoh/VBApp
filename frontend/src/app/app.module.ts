import {APP_INITIALIZER, NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';

import {AppRoutingModule} from './app-routing.module';
import {AppComponent} from './app.component';
import {CoreModule} from "./core/core.module";
import {HTTP_INTERCEPTORS, provideHttpClient, withInterceptorsFromDi} from "@angular/common/http";
import {AuthInterceptor} from "./core/interceptors/auth.interceptor";
import {PendingRequestInterceptor} from "./core/interceptors/pending-request.interceptor";
import {CompetitorModule} from "./competitor/competitor.module";
import {AdminModule} from "./admin/admin.module";
import {UnknownRouteComponent} from './unknown-route/unknown-route.component';
import {ApiKeyHeaderInterceptor} from "./core/interceptors/api-key-header.interceptor";
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {SharedModule} from "./shared/shared.module";
import {LoginModule} from "./login/login.module";
import {VolunteerModule} from "./volunteer/volunteer.module";
import {NotauthorizedInterceptor} from "./core/interceptors/notauthorized.interceptor";
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import {ButtonModule} from "primeng/button";
import {InputTextModule} from "primeng/inputtext";
import {CardModule} from "primeng/card";
import {TokenHeaderInterceptor} from "./core/interceptors/token-header.interceptor";
import {FeedbackInterceptor} from "./core/interceptors/feedback.interceptor";
import {ConfirmationService, MessageService} from "primeng/api";
import {EnvService} from './core/env.service';
import {HashLocationStrategy, LocationStrategy} from '@angular/common';
import {SvgService} from "./shared/svg.service";
import {TrackInfoComponent} from "./competitor/track-info/track-info.component";
import {providePrimeNG} from "primeng/config";
import MyTheme from "./design/myTheme";
import {ToastComponent} from "./shared/components/toast/toast.component";
import {LoaderComponent} from "./core/loader/loader.component";


export function preloadSvgFactory(svgService: SvgService): () => Promise<void> {
  return () => svgService.preloadSvgs();
}

@NgModule({
  declarations: [
    AppComponent,
    UnknownRouteComponent,
    AppComponent,
    TrackInfoComponent
  ],
  exports: [CardModule, SharedModule, AppComponent],
  bootstrap: [AppComponent], imports: [BrowserAnimationsModule,
    ButtonModule,
    CoreModule,
    SharedModule,
    LoginModule,
    CompetitorModule,
    FormsModule,
    AdminModule,
    VolunteerModule,
    FormsModule,
    ReactiveFormsModule,
    AppRoutingModule,
    InputTextModule], providers: [{provide: LocationStrategy, useClass: HashLocationStrategy}, MessageService,
    providePrimeNG({ theme: { preset: MyTheme } }),
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true
    },
    SvgService,
    {
      provide: APP_INITIALIZER,
      useFactory: preloadSvgFactory,
      deps: [SvgService],
      multi: true,
    },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: PendingRequestInterceptor,
      multi: true
    },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: ApiKeyHeaderInterceptor,
      multi: true
    },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: NotauthorizedInterceptor,
      multi: true
    },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: TokenHeaderInterceptor,
      multi: true
    },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: FeedbackInterceptor,
      multi: true
    }, {
      provide: APP_INITIALIZER,
      useFactory: (envService: EnvService) => () => envService.init(),
      deps: [EnvService],
      multi: true
    }, ConfirmationService, provideHttpClient(withInterceptorsFromDi())]
})
export class AppModule {
}
