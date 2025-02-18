import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {ParticipantComponentService} from "../participant-component.service";

@Component({
    selector: 'brevet-participant-list',
    templateUrl: './participant-list.component.html',
    styleUrls: ['./participant-list.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class ParticipantListComponent implements OnInit {

  // $track = this.participantComponentService.$currentTrack

  constructor(private participantComponentService: ParticipantComponentService) { }

  ngOnInit(): void {
    this.participantComponentService.currentTrack(null);
  }

}
