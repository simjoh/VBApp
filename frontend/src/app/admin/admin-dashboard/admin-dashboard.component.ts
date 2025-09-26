import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { Subscription, interval, Observable } from 'rxjs';
import { TrackService } from '../../shared/track-service';
import { EventService } from '../event-admin/event.service';
import { ParticipantService, ParticipantStats, TopTrack } from '../../shared/participant.service';
import { forkJoin, of } from 'rxjs';
import { catchError, map, take } from 'rxjs/operators';
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
  selector: 'app-admin-dashboard',
  templateUrl: './admin-dashboard.component.html',
  styleUrls: ['./admin-dashboard.component.scss']
})
export class AdminDashboardComponent implements OnInit, OnDestroy {
  loading = false;
  stats$: Observable<DashboardStats>;
  todaysTracksCount = 0;
  todaysTracks: any[] = [];
  private refreshSubscription: Subscription;

  constructor(
    private router: Router,
    private eventService: EventService,
    private trackService: TrackService,
    private participantService: ParticipantService
  ) {}

  ngOnInit(): void {
    this.loadDashboardData();
    // Set up auto-refresh every 30 seconds
    this.refreshSubscription = interval(30000).subscribe(() => {
      this.loadDashboardData();
    });
  }

  ngOnDestroy(): void {
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
      map(({ events, tracks, participants, topTracks }) => {
        // Calculate today's tracks count
        const today = new Date();
        const todayString = today.toISOString().split('T')[0];

        const todaysTracks = tracks.filter((track: TrackRepresentation) => {
          if (!track.start_date_time) return false;
          const trackDate = new Date(track.start_date_time).toISOString().split('T')[0];
          return trackDate === todayString;
        });

        this.todaysTracksCount = todaysTracks.length;
        this.todaysTracks = todaysTracks;

        return {
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
        };
      })
    );
  }

  refreshDashboard(): void {
    this.loadDashboardData();
  }

  exportReport(): void {
    console.log('Exporting dashboard report...');
  }

  navigateTo(route: string): void {
    this.router.navigate([route]);
  }

  createNewTrack(): void {
    this.router.navigate(['/admin/banor/new']);
  }

  viewSystemHealth(): void {
    console.log('Viewing system health details...');
  }

  refreshActivity(): void {
    console.log('Refreshing activity feed...');
  }

  getTodaysTracksCount(): number {
    return this.todaysTracksCount;
  }

  getTodaysTracks(): any[] {
    return this.todaysTracks;
  }

  navigateToLatestRegistration(): void {
    if (this.stats$) {
      // Get the latest registration data
      this.stats$.pipe(take(1)).subscribe(stats => {
        if (stats.latestRegistration) {
          // Get all tracks to find the matching one
          this.trackService.getAllTracks().pipe(take(1)).subscribe(allTracks => {
            // Find the track UID by matching the track name
            const matchingTrack = allTracks.find(track =>
              track.title === stats.latestRegistration.track
            );

            if (matchingTrack) {
              console.log('Found matching track:', matchingTrack.title, 'UID:', matchingTrack.track_uid);
              // Navigate to participant page with track filter
              this.router.navigate(['/admin/participant'], {
                queryParams: { track: matchingTrack.track_uid }
              });
            } else {
              console.log('No matching track found for:', stats.latestRegistration.track);
              // If no matching track found, just go to participant page
              this.router.navigate(['/admin/participant']);
            }
          });
        } else {
          // No latest registration, just go to participant page
          this.router.navigate(['/admin/participant']);
        }
      });
    } else {
      // Fallback to participant page
      this.router.navigate(['/admin/participant']);
    }
  }

  getParticipationClass(participantCount: number): string {
    if (participantCount >= 10) {
      return 'high-participation';
    } else if (participantCount >= 5) {
      return 'medium-participation';
    } else {
      return 'low-participation';
    }
  }

  getTrackStatus(track: any): string {
    const now = new Date();
    const firstReg = new Date(track.first_registration);
    const lastReg = new Date(track.last_registration);

    if (now < firstReg) {
      return 'Upcoming';
    } else if (now >= firstReg && now <= lastReg) {
      return 'Active';
    } else {
      return 'Completed';
    }
  }

  getTrackStatusClass(track: any): string {
    const now = new Date();
    const firstReg = new Date(track.first_registration);
    const lastReg = new Date(track.last_registration);

    if (now < firstReg) {
      return 'status-upcoming';
    } else if (now >= firstReg && now <= lastReg) {
      return 'status-active';
    } else {
      return 'status-completed';
    }
  }

  getTrackProgress(track: any): number {
    const now = new Date();
    const firstReg = new Date(track.first_registration);
    const lastReg = new Date(track.last_registration);
    const totalDuration = lastReg.getTime() - firstReg.getTime();
    const elapsed = now.getTime() - firstReg.getTime();

    if (now < firstReg) {
      return 0;
    } else if (now > lastReg) {
      return 100;
    } else {
      return Math.round((elapsed / totalDuration) * 100);
    }
  }

  // Simplified UX methods
  getTrackType(trackName: string): string {
    if (trackName.includes('BRM')) {
      return 'BRM';
    } else if (trackName.match(/\d+/)) {
      const match = trackName.match(/(\d+)/);
      return match ? `${match[1]}km` : 'Distance';
    }
    return 'Track';
  }

  getTrackTypeClass(trackName: string): string {
    if (trackName.includes('BRM')) {
      return 'brm';
    } else if (trackName.match(/\d+/)) {
      return 'distance';
    }
    return '';
  }

  viewTrackDetails(track: any, event?: Event): void {
    if (event) {
      event.stopPropagation();
    }

    // Navigate to track details or show modal
    console.log('Viewing track details:', track);
    // You can implement navigation or modal opening here
    this.router.navigate(['/admin/banor', track.track_uid]);
  }
}
