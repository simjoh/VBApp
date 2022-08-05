import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {TrackAdminComponent} from "./track-admin.component";
import {UploadTrackComponent} from "./upload-track/upload-track.component";
import {TrackListComponent} from "./track-list/track-list.component";
import {TrackBuilderComponent} from "./track-builder/track-builder.component";

const routes: Routes = [
  {
    path: '',
    component: TrackAdminComponent,
    children: [
      { path: 'brevet-track-upload', component:  UploadTrackComponent},
      { path: 'brevet-track-list', component:  TrackListComponent},
      { path: 'brevet-track-builder', component:  TrackBuilderComponent},
      { path: '**', redirectTo: 'brevet-track-list'},
    ]
  }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class TrackRoutingModule { }
