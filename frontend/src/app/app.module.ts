import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import {CoreModule} from "./core/core.module";
import {HTTP_INTERCEPTORS, HttpClientModule} from "@angular/common/http";
import {AuthInterceptor} from "./core/interceptors/auth.interceptor";
import {PendingRequestInterceptor} from "./core/interceptors/pending-request.interceptor";
import { NgbdTableComplete } from './competitors-list/table-complete';
import { NgbdSortableHeader } from './competitors-list/sortable.directive';
import { CommonModule } from "@angular/common";
import { FormsModule } from '@angular/forms';
@NgModule({
  declarations: [
    AppComponent,
    NgbdTableComplete,
    NgbdSortableHeader,
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    NgbModule,
    CoreModule,
    AppRoutingModule,
    CommonModule,
    FormsModule

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
