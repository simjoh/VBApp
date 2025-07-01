import { Component, OnInit, OnDestroy } from '@angular/core';
import { TrackService } from '../../shared/track-service';
import { EventService } from '../event-admin/event.service';
import { ParticipantService, ParticipantStats, TopTrack } from '../../shared/participant.service';
import { forkJoin, Observable, of, interval, Subscription } from 'rxjs';
import { catchError, map, tap } from 'rxjs/operators';
import { EventRepresentation } from '../../shared/api/api';
import { TrackRepresentation } from '../../shared/api/api';

interface DashboardStats {
  totalEvents: number;
  activeEvents: number;
  totalTracks: number;
  activeTracks: number;
  total: number;
  registered: number;
  started: number;
  finished: number;
  dnf: number;
  dns: number;
  weeklyStats: {
    countparticipants: number;
    started: number;
    completed: number;
    dnf: number;
    dns: number;
  };
  yearlyStats: {
    countparticipants: number;
    started: number;
    completed: number;
    dnf: number;
    dns: number;
  };
  latestRegistration: {
    participant_uid: string;
    name: string;
    club: string;
    track: string;
    registration_time: string;
  } | null;
  topTracks: TopTrack[];
}

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit, OnDestroy {
  stats$: Observable<DashboardStats>;
  private refreshSubscription: Subscription;

  constructor(
    private eventService: EventService,
    private trackService: TrackService,
    private participantService: ParticipantService
  ) {}

  ngOnInit() {
    // Initial load
    this.loadDashboardData();

    // Set up auto-refresh every 30 seconds
    this.refreshSubscription = interval(30000).subscribe(() => {
      this.loadDashboardData();
    });
  }

  ngOnDestroy() {
    // Clean up subscription when component is destroyed
    if (this.refreshSubscription) {
      this.refreshSubscription.unsubscribe();
    }
  }

  private loadDashboardData() {
    this.stats$ = forkJoin({
      events: this.eventService.getAllEvents().pipe(catchError(() => of([]))),
      tracks: this.trackService.getAllTracks().pipe(catchError(() => of([]))),
      participants: this.participantService.getParticipantStats().pipe(catchError(() => of({
        daily: {
          countparticipants: 0,
          started: 0,
          completed: 0,
          dnf: 0,
          dns: 0
        },
        weekly: {
          countparticipants: 0,
          started: 0,
          completed: 0,
          dnf: 0,
          dns: 0
        },
        yearly: {
          countparticipants: 0,
          started: 0,
          completed: 0,
          dnf: 0,
          dns: 0
        },
        latest_registration: null
      } as ParticipantStats))),
      topTracks: this.participantService.getTopTracks().pipe(
        catchError(() => of([]))
      )
    }).pipe(
      tap(data => console.log('All dashboard data:', data)),
      map(({ events, tracks, participants, topTracks }) => ({
        totalEvents: events.length,
        activeEvents: events.filter((event: EventRepresentation) => !event.completed).length,
        totalTracks: tracks.length,
        activeTracks: tracks.filter((track: TrackRepresentation) => track.active).length,
        total: participants.daily.countparticipants || 0,
        registered: participants.daily.countparticipants || 0,
        started: participants.daily.started || 0,
        finished: participants.daily.completed || 0,
        dnf: participants.daily.dnf || 0,
        dns: participants.daily.dns || 0,
        weeklyStats: {
          countparticipants: participants.weekly.countparticipants || 0,
          started: participants.weekly.started || 0,
          completed: participants.weekly.completed || 0,
          dnf: participants.weekly.dnf || 0,
          dns: participants.weekly.dns || 0
        },
        yearlyStats: {
          countparticipants: participants.yearly.countparticipants || 0,
          started: participants.yearly.started || 0,
          completed: participants.yearly.completed || 0,
          dnf: participants.yearly.dnf || 0,
          dns: participants.yearly.dns || 0
        },
        latestRegistration: participants.latest_registration,
        topTracks: topTracks
      }))
    );
  }
}
