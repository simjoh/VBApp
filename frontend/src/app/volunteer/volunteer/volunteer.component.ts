import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {Statistics, VolonteerComponentService} from "../volonteer-component.service";
import {map, mergeMap} from "rxjs/operators";
import {ConfirmationService, SelectItem} from 'primeng/api';
import { DatePipe } from '@angular/common';
import {ParticipantToPassCheckpointRepresentation} from "../../shared/api/api";
import {combineLatest, Observable} from "rxjs";

@Component({
  selector: 'brevet-volunteer',
  templateUrl: './volunteer.component.html',
  styleUrls: ['./volunteer.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [VolonteerComponentService, ConfirmationService]
})
export class VolunteerComponent implements OnInit {

  participantStats$: Observable<Statistics & { expected: number }>;
  expectedParticipants$: Observable<number>;

  constructor(private vol :VolonteerComponentService, private datePipe: DatePipe, public confirmationService: ConfirmationService) {
    this.participantStats$ = this.vol.combinedStats$;
    this.expectedParticipants$ = this.vol.expectedParticipants$;
  }

  ngOnInit(): void {
  }

  trackSelected($event: any) {
    this.vol.valdBana($event);
  }

  getTotalParticipants(stats: Statistics): number {
    // Total participants = checked in + not passed + DNF
    // Since DNS is not available in the checkpoint data, we use not passed as a proxy
    return (stats.countpassed || 0) + (stats.notPassed || 0) + (stats.dnf || 0);
  }

  getStartedParticipants(stats: Statistics & { expected?: number }): number {
    // Participants who started the race = checked in + expected + DNS (excluding DNFs)
    // This is used for percentage calculations of checked in and checked out
    return (stats.countpassed || 0) + (stats.expected || 0) + (stats.dns || 0);
  }

  getTotalStartedParticipants(stats: Statistics): number {
    // Total participants who started the race (consistent across checkpoints)
    // This should be the same at every checkpoint
    return (stats.countpassed || 0) + (stats.notPassed || 0) + (stats.dnf || 0);
  }

  getExpectedPercentage(stats: Statistics & { expected: number }): string {
    // For expected participants, calculate percentage based on total participants (including DNFs)
    // This shows what percentage of all participants are still expected to arrive
    const totalParticipants = this.getTotalParticipants(stats);

    if (totalParticipants > 0) {
      return ((stats.expected || 0) / totalParticipants * 100).toFixed(1);
    }
    return '0';
  }

  getExpectedPercentageText(): string {
    // For the first checkpoint, use "förväntade" instead of "kvarvarande"
    // This could be enhanced later to detect if it's the first checkpoint
    return 'förväntade';
  }


}



