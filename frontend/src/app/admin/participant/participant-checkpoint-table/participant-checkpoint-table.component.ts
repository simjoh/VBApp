import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import {ParticipantCheckpointTableComponentService} from "./participant-checkpoint-table-component.service";
import {
  CheckpointRepresentation,
  ParticipantRepresentation,
  ParticipantToPassCheckpointRepresentation,
  RandonneurCheckPointRepresentation
} from 'src/app/shared/api/api';
import {Statistics} from "../../../volunteer/volonteer-component.service";

@Component({
    selector: 'brevet-participant-checkpoint-table',
    templateUrl: './participant-checkpoint-table.component.html',
    styleUrls: ['./participant-checkpoint-table.component.scss'],
    providers: [ParticipantCheckpointTableComponentService],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class ParticipantCheckpointTableComponent implements OnInit {


  $checkpoints = this.participantcheckpoint.$checkpoints;

  $dim = this.participantcheckpoint.$dimCheckin;

  @Input() participant: ParticipantRepresentation

  constructor(private participantcheckpoint: ParticipantCheckpointTableComponentService) {
  }

  ngOnInit(): void {
    this.participantcheckpoint.initCheckpoints(this.participant);
  }

  checkin(checkpoint: RandonneurCheckPointRepresentation) {
      this.participantcheckpoint.checkin(checkpoint);
  }


}






