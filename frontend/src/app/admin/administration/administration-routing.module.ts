import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {TrackAdminComponent} from "../track/track-admin.component";
import {UploadTrackComponent} from "../track/upload-track/upload-track.component";
import {TrackListComponent} from "../track/track-list/track-list.component";
import {TrackBuilderComponent} from "../track/track-builder/track-builder.component";
import {AdministrationComponent} from "./administration.component";
import {AcpReportComponent} from "./acp-report/acp-report.component";
import {
  ParticipantsToReportTableComponent
} from "./participants-to-report-table/participants-to-report-table.component";
import {PrepareAcpReportTableComponent} from "./prepare-acp-report-table/prepare-acp-report-table.component";

const routes: Routes = [
  {
    path: '',
    component: AdministrationComponent,
    children: [
      { path: 'brevet-acp-report', component:  AcpReportComponent},
      { path: 'prepare-report', component:  PrepareAcpReportTableComponent},
      { path: 'to-report/:code', component:  ParticipantsToReportTableComponent},
      { path: '**', redirectTo: 'brevet-acp-report'},
    ]
  }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdministrationRoutingModule { }
