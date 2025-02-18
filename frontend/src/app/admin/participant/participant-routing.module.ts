import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {ParticipantComponent} from "./participant/participant.component";
import {UploadParticipantComponent} from "./upload-participant/upload-participant.component";
import {ParticipantListComponent} from "./participant-list/participant-list.component";



const routes: Routes = [
  {
    path: '',
    component: ParticipantComponent,
    children: [
      { path: 'brevet-participant-upload', component:  UploadParticipantComponent},
      { path: 'brevet-participant-list', component:  ParticipantListComponent},
      { path: '**', redirectTo: 'brevet-participant-list'},
    ]
  }];


@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ParticipantRoutingModule {







}
