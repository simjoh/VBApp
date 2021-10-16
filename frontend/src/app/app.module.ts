import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import {CoreModule} from "./core/core.module";
import {HTTP_INTERCEPTORS, HttpClientModule} from "@angular/common/http";
import {AuthInterceptor} from "./core/interceptors/auth.interceptor";
import {PendingRequestInterceptor} from "./core/interceptors/pending-request.interceptor";
import { CompetitorsListComponent } from './competitors-list/competitors-list.component';
import { CommonModule } from "@angular/common";
@NgModule({
  declarations: [
    AppComponent,
    CompetitorsListComponent,
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    NgbModule,
    CoreModule,
    AppRoutingModule,
    CommonModule,

  ],
  providers: [ {
    provide: HTTP_INTERCEPTORS,
    useClass: AuthInterceptor,
    multi: true},	{
    provide: HTTP_INTERCEPTORS,
    useClass: PendingRequestInterceptor,
    multi: true
  }],
  bootstrap: [AppComponent]
})
export class AppModule { }
