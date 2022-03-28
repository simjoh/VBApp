import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {ParticipantComponent} from "./participant/participant.component";
import {AdminStartComponent} from "../admin-start/admin-start.component";
import {UploadParticipantComponent} from "./upload-participant/upload-participant.component";
import {KontrollerCombinerComponent} from "../kontroller-combiner/kontroller-combiner.component";



const routes: Routes = [
  {
    path: '',
    component: ParticipantComponent,
    children: [
      { path: 'brevet-participant-upload', component:  UploadParticipantComponent},
      { path: 'brevet-participant-list', component:  KontrollerCombinerComponent},
      { path: '**', redirectTo: ''},
    ]
  }];


@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ParticipantRoutingModule {







}
