import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {ParticipantComponentService} from "../participant-component.service";
import {map, mergeMap, startWith} from "rxjs/operators";
import {CheckpointRepresentation, ParticipantInformationRepresentation, ParticipantRepresentation} from "../../../shared/api/api";
import {BehaviorSubject, combineLatest, interval} from "rxjs";

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



  constructor(private participantComponentService: ParticipantComponentService) { }

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

  private searchDisabled(vals: ParticipantInformationRepresentation[]){
    if (vals.length > 0){
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
      if (participant.dnf === true){
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
    if (!started){
      if (dns === true){
        return "Ångra DNS";
      } else {
        return "DNS";
      }
    } else {
      return "DNS";
    }
  }

  dns(participant: ParticipantRepresentation) {
    if (participant.dns === true){
      this.participantComponentService.rollbackDns(participant);
    } else {
      this.participantComponentService.dns(participant);
    }

  }
}
