import {APP_INITIALIZER, NgModule, LOCALE_ID } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { registerLocaleData } from '@angular/common';
import localeSv from '@angular/common/locales/sv';
import localeEn from '@angular/common/locales/en';

// Register locale data globally
registerLocaleData(localeSv, 'sv-SE');
registerLocaleData(localeEn, 'en-US');

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import {CoreModule} from "./core/core.module";
import { HTTP_INTERCEPTORS, provideHttpClient, withInterceptorsFromDi } from "@angular/common/http";
import {AuthInterceptor} from "./core/interceptors/auth.interceptor";
import {PendingRequestInterceptor} from "./core/interceptors/pending-request.interceptor";
import {CompetitorModule} from "./competitor/competitor.module";
import {AdminModule} from "./admin/admin.module";
import { UnknownRouteComponent } from './unknown-route/unknown-route.component';
import {ApiKeyHeaderInterceptor} from "./core/interceptors/api-key-header.interceptor";
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {SharedModule} from "./shared/shared.module";
import {LoginModule} from "./login/login.module";
import {KontrollFormComponent} from "./admin/kontroll-form/kontroll-form.component";
import {KontrollerComponent} from "./admin/kontroller/kontroller.component";
import {NgbdTableComplete} from "./admin/competitors-list/competitors-table-complete";
import {NgbdSortableHeader} from "./admin/competitors-list/sortable.directive";
import {KontrollerCombinerComponent} from "./admin/kontroller-combiner/kontroller-combiner.component";
import {VolunteerModule} from "./volunteer/volunteer.module";
import {NotauthorizedInterceptor} from "./core/interceptors/notauthorized.interceptor";
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import {ButtonModule} from "primeng/button";
import {InputTextModule} from "primeng/inputtext";
import {CardModule} from "primeng/card";
import {TokenHeaderInterceptor} from "./core/interceptors/token-header.interceptor";
import {FeedbackInterceptor} from "./core/interceptors/feedback.interceptor";
import {LoppserviceTokenInterceptor} from "./core/interceptors/loppservice-token.interceptor";
import {LoppserviceApiKeyInterceptor} from "./core/interceptors/loppservice-api-key.interceptor";
import {ConfirmationService, MessageService} from "primeng/api";
import { EnvService } from './core/env.service';
import {HashLocationStrategy, LocationStrategy } from '@angular/common';
import {FlagLanguageSelectorComponent} from "./shared/components/flag-language-selector/flag-language-selector.component";
import {TranslationPipe} from "./shared/pipes/translation.pipe";
import {SidebarModule} from "./core/sidebar/sidebar.module";
import {LanguageService} from "./core/services/language.service";

@NgModule({ declarations: [
        AppComponent,
        UnknownRouteComponent,
        AppComponent,
        NgbdTableComplete,
        NgbdSortableHeader,
        KontrollerComponent,
        KontrollFormComponent,
        KontrollerCombinerComponent,
    ],
    exports: [CardModule, NgbModule, SharedModule, AppComponent, FlagLanguageSelectorComponent, TranslationPipe],
    bootstrap: [AppComponent], imports: [BrowserAnimationsModule,
        ButtonModule,
        CoreModule,
        SharedModule,
        LoginModule,
        CompetitorModule,
        FormsModule,
        AdminModule,
        NgbModule,
        VolunteerModule,
        SidebarModule,
        FormsModule,
        ReactiveFormsModule,
        AppRoutingModule,
        InputTextModule,
        FlagLanguageSelectorComponent,
        TranslationPipe], providers: [{ provide: LocationStrategy, useClass: HashLocationStrategy }, MessageService,
        {
            provide: HTTP_INTERCEPTORS,
            useClass: AuthInterceptor,
            multi: true
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
        },
        {
            provide: HTTP_INTERCEPTORS,
            useClass: LoppserviceTokenInterceptor,
            multi: true
        },
        {
            provide: HTTP_INTERCEPTORS,
            useClass: LoppserviceApiKeyInterceptor,
            multi: true
        }, {
            provide: APP_INITIALIZER,
            useFactory: (envService: EnvService) => () => envService.init(),
            deps: [EnvService],
            multi: true
        }, ConfirmationService, provideHttpClient(withInterceptorsFromDi()),
        {
          provide: LOCALE_ID,
          useFactory: (languageService: any) => {
            const currentLang = languageService.getCurrentLanguage();
            return currentLang === 'sv' ? 'sv-SE' : 'en-US';
          },
          deps: [LanguageService]
        }] })
export class AppModule { }
