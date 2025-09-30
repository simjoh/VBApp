import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { SharedModule } from '../shared/shared.module';
import { DeveloperRoutingModule } from './developer-routing.module';
import { DeveloperComponent } from './developer.component';
import { DeveloperDashboardComponent } from './developer-dashboard/developer-dashboard.component';
import { ApiTestingComponent } from './api-testing/api-testing.component';
import { DeveloperStatsService } from './services/developer-stats.service';

@NgModule({
  declarations: [
    DeveloperComponent,
    DeveloperDashboardComponent,
    ApiTestingComponent
  ],
  imports: [
    CommonModule,
    HttpClientModule,
    SharedModule,
    DeveloperRoutingModule
  ],
})
export class DeveloperModule { }
