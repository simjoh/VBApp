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
import { DialogService } from 'primeng/dynamicdialog';
import { EditCheckpointTimeDialogComponent } from '../edit-checkpoint-time-dialog/edit-checkpoint-time-dialog.component';
import { DateTimePrettyPrintPipe } from 'src/app/shared/pipes/date-time-pretty-print.pipe';

@Component({
  selector: 'brevet-participant-checkpoint-table',
  templateUrl: './participant-checkpoint-table.component.html',
  styleUrls: ['./participant-checkpoint-table.component.scss'],
  providers:[ParticipantCheckpointTableComponentService, DialogService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantCheckpointTableComponent implements OnInit {

  $checkpoints = this.participantcheckpoint.$checkpoints;

  $dim = this.participantcheckpoint.$dimCheckin;

  @Input() participant: ParticipantRepresentation

  constructor(
    public participantcheckpoint: ParticipantCheckpointTableComponentService,
    private dialogService: DialogService
  ) {
  }

  ngOnInit(): void {
    this.participantcheckpoint.initCheckpoints(this.participant);
  }

  checkin(checkpoint: RandonneurCheckPointRepresentation) {
    this.participantcheckpoint.checkin(checkpoint);
  }

  checkout(checkpoint: RandonneurCheckPointRepresentation) {
    this.participantcheckpoint.checkout(checkpoint);
  }

  getPrettyTooltip(timestamp: string): string {
    return DateTimePrettyPrintPipe.getPrettyDate(timestamp);
  }

  editCheckpointTime(checkpoint: RandonneurCheckPointRepresentation) {
    const ref = this.dialogService.open(EditCheckpointTimeDialogComponent, {
      data: {
        time: checkpoint.stamptime,
        address: checkpoint.checkpoint?.site?.adress || '',
        place: checkpoint.checkpoint?.site?.place || '',
        isCheckout: false
      },
      header: null,
      width: '350px',
      contentStyle: {
        padding: '0',
        borderRadius: 'var(--border-radius)',
        overflow: 'hidden'
      },
      baseZIndex: 10000,
      style: {
        maxWidth: '95vw',
        borderRadius: 'var(--border-radius)'
      },
      showHeader: false,
      closeOnEscape: true,
      dismissableMask: true,
      modal: true
    });

    ref.onClose.subscribe((newTime: Date) => {
      if (newTime) {
        this.participantcheckpoint.updateCheckpointTime(checkpoint, newTime);
      }
    });
  }

  editCheckoutTime(checkpoint: RandonneurCheckPointRepresentation) {
    const ref = this.dialogService.open(EditCheckpointTimeDialogComponent, {
      data: {
        time: checkpoint.checkouttime,
        address: checkpoint.checkpoint?.site?.adress || '',
        place: checkpoint.checkpoint?.site?.place || '',
        isCheckout: true
      },
      header: null,
      width: '350px',
      contentStyle: {
        padding: '0',
        borderRadius: 'var(--border-radius)',
        overflow: 'hidden'
      },
      baseZIndex: 10000,
      style: {
        maxWidth: '95vw',
        borderRadius: 'var(--border-radius)'
      },
      showHeader: false,
      closeOnEscape: true,
      dismissableMask: true,
      modal: true
    });

    ref.onClose.subscribe((newTime: Date) => {
      if (newTime) {
        this.participantcheckpoint.updateCheckoutTime(checkpoint, newTime);
      }
    });
  }
}






