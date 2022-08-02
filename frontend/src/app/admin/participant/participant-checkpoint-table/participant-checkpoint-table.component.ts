import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import {ParticipantCheckpointTableComponentService} from "./participant-checkpoint-table-component.service";
import {BehaviorSubject, combineLatest, interval} from "rxjs";
import {map, mergeMap, startWith} from "rxjs/operators";
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
  providers:[ParticipantCheckpointTableComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantCheckpointTableComponent implements OnInit {


  $checkpoints = this.participantcheckpoint.$checkpoints;

  $dim = this.participantcheckpoint.$dimCheckin;

  @Input() participant: ParticipantRepresentation

  constructor(private participantcheckpoint: ParticipantCheckpointTableComponentService) {
  }

  ngOnInit(): void {

    // var a="14:10";
    // var b="19:02";
    //
    // var date1=new Date("01-01-2017 " + a);
    // var date2=new Date("01-01-2017 " + b + ":00");
    //
    // console.log(date1.getHours())

    this.participantcheckpoint.initCheckpoints(this.participant);
  }

  checkin(checkpoint: RandonneurCheckPointRepresentation) {
      this.participantcheckpoint.checkin(checkpoint);
  }


}






