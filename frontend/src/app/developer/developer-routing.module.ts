import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { DeveloperComponent } from './developer.component';
import { DeveloperDashboardComponent } from './developer-dashboard/developer-dashboard.component';
import { ApiTestingComponent } from './api-testing/api-testing.component';
import { AuthenticatedGuard } from '../core/auth/authenticated.guard';

const routes: Routes = [
  {
    path: '',
    component: DeveloperComponent,
    canActivate: [AuthenticatedGuard],
    children: [
      {
        path: '',
        component: DeveloperDashboardComponent
      },
      {
        path: 'api-testing',
        component: ApiTestingComponent
      }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DeveloperRoutingModule { }
