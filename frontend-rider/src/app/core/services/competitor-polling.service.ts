import { Injectable, inject, signal } from '@angular/core';
import { Observable, BehaviorSubject, combineLatest, map, tap, catchError, of } from 'rxjs';
import { VisibilityAwarePollingService, PollingConfig } from './visibility-aware-polling.service';
import { MessageService } from './message.service';
import { environment } from '../../../environments/environment';

// Data interfaces
export interface TrackData {
  track_uid: string;
  title: string;
  distance: string;
  start_date_time: string;
  checkpoints: {
    checkpoint_uid: string;
    site: {
      site_uid: string;
      place: string;
      adress: string;
      location: string;
      description: string;
      lat: string;
      lng: string;
      image: string;
      check_in_distance: string;
      links: any[];
    };
    title: string;
    description: string;
    distance: number;
    opens: string;
    closing: string;
  }[];
}

export interface CheckpointData {
  checkpoint_uid: string;
  name: string;
  distance: number;
  opens: string;
  closes: string;
  service: string;
}

export interface ParticipantState {
  participantStatus: 'active' | 'dnf' | 'dns' | 'finished';
  participantUid: string;
  participantName: string;
  trackUid: string;
  startnumber: string;
  checkpointStates: {
    [checkpointUid: string]: {
      status: 'checked-in' | 'checked-out' | 'not-visited';
      timestamp?: string;
      checkoutTimestamp?: string;
    }
  };
  lastUpdated: string;
}

export interface CombinedCheckpointData extends CheckpointData {
  status: 'checked-in' | 'checked-out' | 'not-visited';
  timestamp?: string;
  checkoutTimestamp?: string;
  site?: {
    image?: string;
    adress?: string;
    place?: string;
    description?: string;
  };
}

export interface CombinedCompetitorData {
  track: {
    trackUid: string;
    title: string;
    distance: string;
    startDateTime?: string;
  };
  checkpoints: CombinedCheckpointData[];
  participantStatus: ParticipantState['participantStatus'];
  participantUid: string;
  participantName: string;
  lastUpdated: string;
}

@Injectable({
  providedIn: 'root'
})
export class CompetitorPollingService {
  private pollingService = inject(VisibilityAwarePollingService);
  private messageService = inject(MessageService);

  // Data storage
  private trackData$ = new BehaviorSubject<TrackData | null>(null);
  private participantState$ = new BehaviorSubject<ParticipantState | null>(null);
  private isLoading = signal(false);
  private hasError = signal<string | null>(null);

  // Combined data observable for UI consumption
  combinedData$ = combineLatest([
    this.trackData$,
    this.participantState$
  ]).pipe(
    map(([track, state]) => this.combineTrackWithState(track, state))
  );

  /**
   * Start polling for competitor data
   * @param trackUid Track identifier
   * @param startNumber Participant start number
   * @param pollingConfig Optional custom polling configuration
   */
  startCompetitorPolling(
    trackUid: string,
    startNumber: string,
    pollingConfig: Partial<PollingConfig> = {}
  ): void {
    console.log(`[CompetitorPolling] Starting for track: ${trackUid}, start: ${startNumber}`);

    this.isLoading.set(true);
    this.hasError.set(null);

    // Load static track data once
    this.loadTrackData(trackUid, startNumber);

    // Start polling participant state with visibility awareness
    this.startStatePolling(trackUid, startNumber, pollingConfig);
  }

  /**
   * Load static track data (called once, cached)
   */
  private loadTrackData(trackUid: string, startNumber: string): void {
    const url = `${environment.backend_url}randonneur/trackfor/${trackUid}/startnumber/${startNumber}`;

    console.log(`[CompetitorPolling] Loading track data from: ${url}`);

    this.pollingService.refreshImmediately<TrackData>(url).pipe(
      tap(data => {
        console.log(`[CompetitorPolling] Track data loaded:`, data);
        this.trackData$.next(data);
        this.isLoading.set(false);
        console.log(`[CompetitorPolling] Track data set in BehaviorSubject`);
      }),
      catchError(error => {
        console.error(`[CompetitorPolling] Failed to load track data:`, error);
        this.hasError.set('Failed to load track information');
        this.isLoading.set(false);
        this.messageService.showError('Loading Error', 'Failed to load track information');
        return of(null);
      })
    ).subscribe();
  }

  /**
   * Start visibility-aware polling for participant state
   */
  private startStatePolling(
    trackUid: string,
    startNumber: string,
    pollingConfig: Partial<PollingConfig>
  ): void {
    const url = `${environment.backend_url}randonneur/state/${trackUid}/startnumber/${startNumber}`;

    const config: PollingConfig = {
      interval: 30000, // 30 seconds
      immediateOnWakeup: true,
      progressiveSlowdown: true,
      maxInterval: 120000, // 2 minutes max
      onError: (error) => {
        console.error(`[CompetitorPolling] State polling error:`, error);
        this.hasError.set('Failed to update participant state');
      },
      ...pollingConfig
    };

    console.log(`[CompetitorPolling] Starting state polling with config:`, config);

    this.pollingService.startPolling<ParticipantState>(
      'competitor-state',
      url,
      config
    ).pipe(
      tap(state => {
        if (state) {
          console.log(`[CompetitorPolling] State updated:`, state);
          this.participantState$.next(state);
          this.hasError.set(null);
          console.log(`[CompetitorPolling] State data set in BehaviorSubject`);
        }
      }),
      catchError(error => {
        console.error(`[CompetitorPolling] State polling stream error:`, error);
        console.error(`[CompetitorPolling] Error details:`, {
          status: error.status,
          statusText: error.statusText,
          url: error.url,
          errorBody: error.error,
          headers: error.headers
        });
        this.hasError.set(`Failed to load participant state (${error.status})`);
        return of(null);
      })
    ).subscribe();
  }

  /**
   * Force immediate refresh of participant state
   */
  refreshState(): void {
    const currentState = this.participantState$.value;
    if (currentState) {
      const url = `${environment.backend_url}randonneur/state/${currentState.trackUid}/startnumber/${currentState.startnumber}`;

      console.log(`[CompetitorPolling] Force refreshing state`);

      this.pollingService.refreshImmediately<ParticipantState>(url).pipe(
        tap(state => {
          if (state) {
            this.participantState$.next(state);
          }
        }),
        catchError(error => {
          console.error(`[CompetitorPolling] Force refresh failed:`, error);
          return of(null);
        })
      ).subscribe();
    }
  }

  /**
   * Stop all competitor polling
   */
  stopPolling(): void {
    console.log(`[CompetitorPolling] Stopping all polling`);
    this.pollingService.stopPolling('competitor-state');
  }

  /**
   * Combine track and state data for UI consumption
   */
  private combineTrackWithState(
    track: TrackData | null,
    state: ParticipantState | null
  ): CombinedCompetitorData | null {
    console.log(`[CompetitorPolling] Combining data - Track: ${!!track}, State: ${!!state}`);

    if (!track || !state) {
      console.log(`[CompetitorPolling] Missing data - Track: ${!!track}, State: ${!!state}`);
      return null;
    }

    const combinedCheckpoints: CombinedCheckpointData[] = track.checkpoints.map(checkpoint => {
      const checkpointState = state.checkpointStates[checkpoint.checkpoint_uid];

      return {
        checkpoint_uid: checkpoint.checkpoint_uid,
        name: checkpoint.site?.adress || checkpoint.site?.place || 'Unknown Location',
        distance: checkpoint.distance,
        opens: checkpoint.opens,
        closes: checkpoint.closing,
        service: checkpoint.site?.description || checkpoint.description,
        status: checkpointState?.status || 'not-visited',
        timestamp: checkpointState?.timestamp,
        checkoutTimestamp: checkpointState?.checkoutTimestamp,
        site: checkpoint.site
      };
    });

    return {
      track: {
        trackUid: track.track_uid,
        title: track.title,
        distance: track.distance,
        startDateTime: track.start_date_time
      },
      checkpoints: combinedCheckpoints,
      participantStatus: state.participantStatus,
      participantUid: state.participantUid,
      participantName: state.participantName,
      lastUpdated: state.lastUpdated
    };
  }

  /**
   * Get current track data
   */
  getCurrentTrackData(): TrackData | null {
    return this.trackData$.value;
  }

  /**
   * Get current participant state
   */
  getCurrentParticipantState(): ParticipantState | null {
    return this.participantState$.value;
  }

  /**
   * Get loading state
   */
  getLoadingState() {
    return this.isLoading.asReadonly();
  }

  /**
   * Get error state
   */
  getErrorState() {
    return this.hasError.asReadonly();
  }

  /**
   * Get app visibility state from underlying service
   */
  getVisibilityState(): boolean {
    return this.pollingService.getVisibilityState();
  }

  /**
   * Get polling state from underlying service
   */
  getPollingState() {
    return this.pollingService.getPollingState();
  }

  /**
   * Observable for visibility changes
   */
  getVisibilityChanges(): Observable<boolean> {
    return this.pollingService.getVisibilityChanges();
  }
}
