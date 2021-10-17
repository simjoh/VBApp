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
import { LoginComponent } from './login/login.component';
import {ApiKeyHeaderInterceptor} from "./core/interceptors/api-key-header.interceptor";

import { NgbdTableComplete } from './competitors-list/competitors-table-complete';
import { NgbdSortableHeader } from './competitors-list/sortable.directive';
import { CommonModule } from "@angular/common";
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { KontrollerComponent } from './kontroller/kontroller.component';
import { KontrollFormComponent } from './kontroll-form/kontroll-form.component';
import { KontrollerCombinerComponent } from './kontroller-combiner/kontroller-combiner.component';
import { MapComponent } from './map/map.component';
@NgModule({
  declarations: [
    AppComponent,
    UnknownRouteComponent,
    LoginComponent,
    AppComponent,
    NgbdTableComplete,
    NgbdSortableHeader,
    KontrollerComponent,
    KontrollFormComponent,
    KontrollerCombinerComponent,
    MapComponent,
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    CompetitorModule,
    FormsModule,
    AdminModule,
    NgbModule,
    CoreModule,
    AppRoutingModule,
    CommonModule,
    FormsModule,
    ReactiveFormsModule

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
