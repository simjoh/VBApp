import {NgModule} from '@angular/core';

import {AdministrationRoutingModule} from './administration-routing.module';
import {AdministrationComponent} from "./administration.component";
import {SharedModule} from "../../shared/shared.module";
import {PrepareAcpReportTableComponent} from "./prepare-acp-report-table/prepare-acp-report-table.component";
import {
  ParticipantsToReportTableComponent
} from "./participants-to-report-table/participants-to-report-table.component";


@NgModule({
  declarations: [
    AdministrationComponent,
    ParticipantsToReportTableComponent,
    PrepareAcpReportTableComponent
  ],
  imports: [
    SharedModule,
    AdministrationRoutingModule

  ]
})
export class AdministrationModule {
}
