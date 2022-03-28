import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {ParticipantComponent} from "../participant/participant/participant.component";
import {UploadParticipantComponent} from "../participant/upload-participant/upload-participant.component";
import {KontrollerCombinerComponent} from "../kontroller-combiner/kontroller-combiner.component";
import {TrackAdminComponent} from "./track-admin/track-admin.component";
import {UploadTrackComponent} from "./upload-track/upload-track.component";

const routes: Routes = [
  {
    path: '',
    component: TrackAdminComponent,
    children: [
      { path: 'brevet-track-upload', component:  UploadTrackComponent},
      { path: '**', redirectTo: ''},
    ]
  }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class TrackRoutingModule { }
