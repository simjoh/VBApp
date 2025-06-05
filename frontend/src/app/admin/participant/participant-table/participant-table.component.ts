import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {ParticipantComponentService} from "../participant-component.service";
import {map, startWith} from "rxjs/operators";
import {ParticipantInformationRepresentation, ParticipantRepresentation} from "../../../shared/api/api";
import {BehaviorSubject, interval} from "rxjs";
import {DialogService} from "primeng/dynamicdialog";
import {EditTimeDialogComponent} from "../edit-time-dialog/edit-time-dialog.component";
import {EditBrevenrDialogComponent} from "../edit-brevenr-dialog/edit-brevenr-dialog.component";
import {environment} from "../../../../environments/environment";
import {HttpClient} from "@angular/common/http";
import { saveAs } from 'file-saver';

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


  constructor(
    private participantComponentService: ParticipantComponentService,
    private dialogService: DialogService,
    private http: HttpClient
  ) {
  }

  ngOnInit(): void {
    this.intervalSub = interval(60000).pipe(
      startWith(0),
    ).subscribe(data => this.participantComponentService.reload());
    this.$serachDisabledSubject.next(true);

    // Subscribe to track changes
    this.participantComponentService.$currentTrackUid.subscribe(trackUid => {
      console.log('Current track UID:', trackUid);
    });
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


  editbrevenr(participant: ParticipantRepresentation) {
    console.log(participant.brevenr);
    const ref = this.dialogService.open(EditBrevenrDialogComponent, {
      data: {
        brevenr: participant.brevenr
      },
      header: 'Lägg till brevenr',
    });

    ref.onClose.subscribe((brevenr: string) => {
      if (brevenr) {
        participant.brevenr = brevenr
        this.participantComponentService.addbrevenr(participant);
        this.participantComponentService.reload();
      }
    });
  }

  getCurrentTrack():boolean {
    return false;
  }

  exportHomologation() {
    const trackUid = this.participantComponentService.getCurrentTrackUid();
    console.log('Track UID:', trackUid);

    if (!trackUid) {
      console.error('No track UID available');
      return;
    }

    const url = environment.backend_url + 'participants/track/' + trackUid + '/report/export';
    console.log('Export URL:', url);

    this.http.get(url, {
      responseType: 'blob',
      headers: {
        'Accept': 'text/csv; charset=utf-8'
      }
    }).subscribe({
      next: (response: Blob) => {
        console.log('Response received:', response);

        // Generate filename based on current date
        const date = new Date().toISOString().split('T')[0];
        const filename = `Homologation_${date}.csv`;

        saveAs(response, filename);
      },
      error: (error) => {
        console.error('Export error:', error);
      }
    });
  }
}
