import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {ParticipantComponentService} from "../participant-component.service";
import {map, startWith} from "rxjs/operators";
import {ParticipantInformationRepresentation, ParticipantRepresentation} from "../../../shared/api/api";
import {BehaviorSubject, interval} from "rxjs";
import {DialogService} from "primeng/dynamicdialog";
import {EditTimeDialogComponent} from "../edit-time-dialog/edit-time-dialog.component";

@Component({
  selector: 'brevet-participant-table',
  templateUrl: './participant-table.component.html',
  styleUrls: ['./participant-table.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantTableComponent implements OnInit {


  $serachDisabledSubject = new BehaviorSubject(true)
  $searchDisabled = this.$serachDisabledSubject.asObservable()


  $participant = this.participantComponentService.$participantsfortrack.pipe(
    map((vals) => {
      this.searchDisabled(vals)
      return vals
    })
  );

  $dimmadnfbuttonSubject = new BehaviorSubject(true);
  $dimDnf = this.$dimmadnfbuttonSubject.asObservable();

  $dimmadnsbuttonSubject = new BehaviorSubject(true);
  $dimDns = this.$dimmadnsbuttonSubject.asObservable();
  intervalSub: any;


  constructor(private participantComponentService: ParticipantComponentService, private dialogService: DialogService) {
  }

  ngOnInit(): void {
    this.intervalSub = interval(60000).pipe(
      startWith(0),
    ).subscribe(data => this.participantComponentService.reload());
    this.$serachDisabledSubject.next(true);
  }

  isPossibleToDelete(participant: ParticipantRepresentation) {
    return this.participantComponentService.isPossibleToRemove(participant);
  }

  remove(participant: any) {
    this.participantComponentService.remove(participant).then(() => {
      this.participantComponentService.reload();
    });

  }

  private searchDisabled(vals: ParticipantInformationRepresentation[]) {
    if (vals.length > 0) {
      this.$serachDisabledSubject.next(false);
    } else {
      this.$serachDisabledSubject.next(true);
    }
  }


  test(participant_uid: any) {
    this.participantComponentService.setCurrentparticipant(participant_uid)
  }

  currentparticipant(participant: ParticipantRepresentation) {
    this.test(participant);
  }

  dnf(participant: ParticipantRepresentation) {
    if (participant.dnf === true) {
      this.participantComponentService.rollbackdnf(participant);
    } else {
      this.participantComponentService.dnf(participant);
    }

  }

  textDnfButton(started: boolean, dnf: any): string {
    if (!started) {
      return "DNF";
    } else {
      if (!dnf) {
        return "DNF";
      } else {
        return "Ångra DNF";
      }
    }
  }

  textDnsButton(started: boolean, dns: boolean): string {
    if (!started) {
      if (dns === true) {
        return "Ångra DNS";
      } else {
        return "DNS";
      }
    } else {
      return "DNS";
    }
  }

  dns(participant: ParticipantRepresentation) {
    if (participant.dns === true) {
      this.participantComponentService.rollbackDns(participant);
    } else {
      this.participantComponentService.dns(participant);
    }

  }

  editTotalTime(participant: ParticipantRepresentation) {

    const ref = this.dialogService.open(EditTimeDialogComponent, {
      data: {
        time: participant.time
      },
      header: 'Ändra sluttid',
    });

    ref.onClose.subscribe((newTime: string) => {
      if (newTime) {
        participant.time = newTime
        this.participantComponentService.updateTime(participant);
        this.participantComponentService.reload();
      }
    });
  }
}
