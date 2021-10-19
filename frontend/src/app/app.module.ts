import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import {CoreModule} from "./core/core.module";
import {HTTP_INTERCEPTORS, HttpClientModule} from "@angular/common/http";
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
@NgModule({
  declarations: [
    AppComponent,
    UnknownRouteComponent,
    AppComponent,
    NgbdTableComplete,
    NgbdSortableHeader,
    KontrollerComponent,
    KontrollFormComponent,
    KontrollerCombinerComponent,
  ],
  imports: [
    BrowserModule,
    CoreModule,
    SharedModule,
    LoginModule,
    HttpClientModule,
    CompetitorModule,
    FormsModule,
    AdminModule,
    NgbModule,
    VolunteerModule,
    FormsModule,
    ReactiveFormsModule,
    AppRoutingModule,

  ],
  providers: [ {
    provide: HTTP_INTERCEPTORS,
    useClass: AuthInterceptor,
    multi: true},	{
    provide: HTTP_INTERCEPTORS,
    useClass: PendingRequestInterceptor,
    multi: true
  },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: ApiKeyHeaderInterceptor,
      multi: true
    }],
  bootstrap: [AppComponent]
})
export class AppModule { }
