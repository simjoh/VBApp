import { Component, inject, signal, OnInit, OnDestroy, ViewChild, AfterViewInit, effect } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { GeolocationService } from '../../core/services/geolocation.service';
import { MessageService } from '../../core/services/message.service';
import { AuthService } from '../../core/services/auth.service';
import { CompetitorHeaderComponent } from '../../shared/components/competitor-header/competitor-header.component';
import { CheckpointCardComponent, CheckpointData } from '../../shared/components/checkpoint-card/checkpoint-card.component';
import { CompetitorPollingService, CombinedCheckpointData } from '../../core/services/competitor-polling.service';
import { TranslationService } from '../../core/services/translation.service';
import { TranslationPipe } from '../../shared/pipes/translation.pipe';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-competitor',
  standalone: true,
  imports: [CommonModule, CompetitorHeaderComponent, CheckpointCardComponent, TranslationPipe],
  templateUrl: './competitor.component.html',
  styleUrl: './competitor.component.scss'
})
export class CompetitorComponent implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild('headerComponent') headerComponent!: CompetitorHeaderComponent;

  private router = inject(Router);
  private geolocationService = inject(GeolocationService);
  private messageService = inject(MessageService);
  private authService = inject(AuthService);
  private competitorService = inject(CompetitorPollingService);
  private http = inject(HttpClient);
  private translationService = inject(TranslationService);

  hasGeolocationPermission = signal<boolean | null>(null);
  isCheckingPermission = signal(true);
  currentCoordinates = signal<{ latitude: number; longitude: number } | null>(null);
  lastPositionUpdate = signal<Date | null>(null);
  private freshnessCheckInterval: any = null;

  // Memory leak prevention
  private effectRef?: any;
  private subscriptions: any[] = [];

  // Backend data
  competitorData$ = this.competitorService.combinedData$;
  checkpoints = signal<CheckpointData[]>([]);
  trackInfo = signal<any>(null);
  participantInfo = signal<any>(null);
  participantName = signal<string>('Loading...');
  isAbandoned = signal<boolean>(false);

  // Progress tracking
  cyclingProgress = signal<{ currentDistance: number; totalDistance: number; progressPercentage: number }>({
    currentDistance: 0,
    totalDistance: 0,
    progressPercentage: 0
  });

  constructor() {
    // Initialize position updates subscription in constructor (injection context)
    this.effectRef = effect(() => {
      const position = this.geolocationService.currentPosition$();
      if (position) {
        const now = new Date();
        this.currentCoordinates.set({
          latitude: position.coords.latitude,
          longitude: position.coords.longitude
        });
        this.lastPositionUpdate.set(now);
      }
    });
  }

  ngOnInit() {
    // Initialize geolocation permission monitoring
    this.geolocationService.initializePermissionMonitoring();

    // Check if user just granted permission
    const justGranted = localStorage.getItem('geolocationJustGranted');

    if (justGranted === 'true') {
      // User just granted permission, don't check again
      console.log('User just granted permission, skipping permission check');
      localStorage.removeItem('geolocationJustGranted');
      this.hasGeolocationPermission.set(true);
      this.isCheckingPermission.set(false);
      this.startLocationTracking();
    } else {
      // Check geolocation permission normally
      this.checkGeolocationPermission();
    }

    this.loadCompetitorData();
    this.startFreshnessCheck();
  }

  ngAfterViewInit() {
    // Component view initialized
  }

  /**
   * Trigger the globe rotation animation to indicate location update
   */
  triggerLocationUpdateAnimation() {
    if (this.headerComponent) {
      this.headerComponent.triggerLocationUpdate();
    }
  }

  /**
   * Start background location tracking every 10 minutes
   */
  private startLocationTracking() {
    console.log('Starting location tracking...');

    // Get initial position to display coordinates
    this.updateCurrentCoordinates();

    // Start background tracking
    // For testing: use 1 minute interval, change to 10 for production
    const intervalMinutes = 1; // Change to 10 for production

    this.geolocationService.startBackgroundTracking({
      enabled: true,
      intervalMinutes: intervalMinutes,
      apiEndpoint: this.getLocationUpdateEndpoint(),
      wakeLockEnabled: false, // Don't keep screen awake
      backgroundSyncEnabled: true // Enable offline sync
    }).then(() => {
      console.log('Background location tracking started successfully!');
      console.log(`Location will be updated every ${intervalMinutes} minute(s)`);
      console.log('API endpoint:', this.getLocationUpdateEndpoint());
      console.log('Check console for background tracking logs...');
    }).catch(error => {
      console.error('Failed to start location tracking:', error);
    });
  }


  /**
   * Update current coordinates from geolocation service
   */
  private updateCurrentCoordinates() {
    const subscription = this.geolocationService.getCurrentPosition().subscribe({
      next: (position) => {
        const now = new Date();
        this.currentCoordinates.set({
          latitude: position.coords.latitude,
          longitude: position.coords.longitude
        });
        this.lastPositionUpdate.set(now);
      },
      error: (error) => {
        console.warn('Failed to get current position:', error);
      }
    });
    this.subscriptions.push(subscription);
  }

  /**
   * Stop background location tracking
   */
  private stopLocationTracking() {
    this.geolocationService.stopBackgroundTracking();
  }


  /**
   * Get the location update API endpoint
   */
  private getLocationUpdateEndpoint(): string {
    const activeUser = this.authService.getActiveUser();
    if (!activeUser?.id || !activeUser?.trackuid || !activeUser?.startnumber) {
      return '';
    }

    // Use a generic location update endpoint
    // This can be adjusted when the backend endpoint is defined
    return `${environment.backend_url}randonneur/${activeUser.id}/track/${activeUser.trackuid}/startnumber/${activeUser.startnumber}/location`;
  }

  ngOnDestroy() {
    // Stop polling when component is destroyed
    this.competitorService.stopPolling();
    this.stopLocationTracking();
    this.stopFreshnessCheck();

    // Clean up geolocation service
    this.geolocationService.cleanup();

    // Clean up all subscriptions to prevent memory leaks
    this.subscriptions.forEach(subscription => {
      if (subscription && typeof subscription.unsubscribe === 'function') {
        subscription.unsubscribe();
      }
    });
    this.subscriptions = [];

    // Clean up effect to prevent memory leaks
    if (this.effectRef && typeof this.effectRef.destroy === 'function') {
      this.effectRef.destroy();
      this.effectRef = undefined;
    }
  }

  private loadCompetitorData() {
    // Get authenticated user data
    const activeUser = this.authService.getActiveUser();
    const token = localStorage.getItem('riderToken');

    // Check authentication status
    const isAuthenticated = !!token && !!activeUser;

    if (!activeUser) {
      this.messageService.showError('Authentication', 'No user session found. Please login again.');
      this.router.navigate(['/login']);
      return;
    }

    if (!activeUser.trackuid || !activeUser.startnumber) {
      this.messageService.showError('Missing Data', 'Unable to load competitor information. User missing track or start number data.');
      return;
    }

    // Start polling for competitor data using real backend
    this.competitorService.startCompetitorPolling(
      activeUser.trackuid,
      activeUser.startnumber,
      {
        interval: 30000,        // 30 seconds
        immediateOnWakeup: true,
        progressiveSlowdown: true,
        maxInterval: 120000     // 2 minutes max
      }
    );

    // Subscribe to combined data
    const dataSubscription = this.competitorData$.subscribe({
      next: (data) => {
        if (data) {

          // Update track info
          this.trackInfo.set({
            title: data.track.title,
            distance: data.track.distance + ' km',
            totalDistanceKm: parseFloat(data.track.distance)
          });

          // Update checkpoints with backend data - transform to match CheckpointData interface
          if (data.checkpoints && Array.isArray(data.checkpoints) && data.checkpoints.length > 0) {
            const transformedCheckpoints: CheckpointData[] = data.checkpoints.map((cp: any, index: number) => {
              // Calculate distance to next checkpoint
              const nextCheckpoint = data.checkpoints[index + 1];
              const toNext = nextCheckpoint ? nextCheckpoint.distance - cp.distance : 0;

              return {
                checkpoint_uid: cp.checkpoint_uid,
                name: cp.site?.adress || cp.site?.place || 'Unknown Location',
                distance: cp.distance,
                toNext: toNext,
                opens: cp.opens,
                closes: cp.closing || cp.closes, // Backend uses 'closing' field
                service: cp.site?.description || 'Checkpoint',
                time: cp.timestamp || '', // Use timestamp as time if available
                logoFileName: cp.site?.image ? cp.site.image.replace('/api/uploads/', '') : undefined, // Keep for backward compatibility
                status: cp.status || 'not-visited',
                timestamp: cp.timestamp,
                checkoutTimestamp: cp.checkoutTimestamp,
                site: cp.site, // Preserve the full site object with original image path
                isFirst: index === 0,
                isLast: index === data.checkpoints.length - 1
              };
            });
            this.checkpoints.set(transformedCheckpoints);

            // Calculate and update progress
            this.updateProgress();
          } else {
            // Handle case when track has no checkpoints
            this.checkpoints.set([]);
            this.messageService.showInfo('Checkpoints Coming Soon', 'Checkpoints for this track will be available soon.');

            // Reset progress when no checkpoints
            this.cyclingProgress.set({
              currentDistance: 0,
              totalDistance: 0,
              progressPercentage: 0
            });
          }

              // Update participant info
              this.participantInfo.set({
                status: data.participantStatus,
                uid: data.participantUid,
                lastUpdated: data.lastUpdated
              });

              // Update abandon status
              this.isAbandoned.set(data.participantStatus === 'dnf');

              // Update participant name
              this.participantName.set(data.participantName || 'Rider #' + (this.currentUser?.startnumber || '000'));
        }
      },
      error: (error) => {
        this.messageService.showError('Data Error', 'Failed to load competitor data');
      }
    });
    this.subscriptions.push(dataSubscription);
  }

  // Get current user data for templates
  get currentUser() {
    return this.authService.getActiveUser();
  }

  // Get location status for header indicator
  getLocationStatus(): 'granted' | 'denied' | 'unknown' {
    if (this.hasGeolocationPermission()) {
      return 'granted';
    } else {
      return 'unknown';
    }
  }

  // Get participant name from backend data
  getParticipantName(): string {
    return this.participantName();
  }

  // Check if location is fresh (1 minute or less old)
  isLocationFresh(): boolean {
    const lastUpdate = this.lastPositionUpdate();
    if (!lastUpdate) return false;

    const now = new Date();
    const diffInMinutes = (now.getTime() - lastUpdate.getTime()) / (1000 * 60);
    return diffInMinutes <= 1;
  }

  /**
   * Start periodic freshness check
   */
  private startFreshnessCheck() {
    // Check every 30 seconds to update freshness status
    this.freshnessCheckInterval = setInterval(() => {
      // Force change detection by updating a signal
      // This will trigger the isLocationFresh() method to re-evaluate
      this.lastPositionUpdate.set(this.lastPositionUpdate());
    }, 30000);
  }

  /**
   * Stop periodic freshness check
   */
  private stopFreshnessCheck() {
    if (this.freshnessCheckInterval) {
      clearInterval(this.freshnessCheckInterval);
      this.freshnessCheckInterval = null;
    }
  }

  private async checkGeolocationPermission() {
    this.isCheckingPermission.set(true);

    try {
      // Check if geolocation is supported
      if (!this.geolocationService.isSupported$()) {
        this.hasGeolocationPermission.set(false);
        this.isCheckingPermission.set(false);
        return;
      }

      // Wait a moment for permission state to settle
      await new Promise(resolve => setTimeout(resolve, 1000));

      // Check current permission status
      const permission = await navigator.permissions.query({ name: 'geolocation' as PermissionName });

      console.log('Checking permission state:', permission.state);

      if (permission.state === 'granted') {
        console.log('Permission granted, starting location tracking');
        this.hasGeolocationPermission.set(true);
        this.isCheckingPermission.set(false);
        this.startLocationTracking();
        return;
      } else if (permission.state === 'prompt') {
        // Permission is in prompt state, redirect to permission page
        console.log('Permission in prompt state, redirecting to permission page');
        this.hasGeolocationPermission.set(false);
        this.isCheckingPermission.set(false);
        this.router.navigate(['/geolocation-permission']);
      } else {
        // Permission denied, redirect to permission page
        console.log('Permission denied, redirecting to permission page');
        this.hasGeolocationPermission.set(false);
        this.isCheckingPermission.set(false);
        this.router.navigate(['/geolocation-permission']);
      }
    } catch (error) {
      console.error('Error checking permission:', error);
      // Error checking permission - redirect to permission page
      this.hasGeolocationPermission.set(false);
      this.isCheckingPermission.set(false);
      this.router.navigate(['/geolocation-permission']);
    }
  }

  requestLocationPermission() {
    this.router.navigate(['/geolocation-permission']);
  }

  logout() {
    this.authService.logout();
    this.router.navigate(['/login']);
  }

  async abandonBrevet() {
    if (this.isAbandoned()) {
      await this.undoAbandonBrevet();
      return;
    }

    const confirmed = confirm(this.translationService.translate('competitor.abandonConfirm'));
    if (!confirmed) return;

    const currentUser = this.currentUser;
    if (!currentUser?.trackuid || !currentUser?.startnumber || !currentUser?.id) {
      this.messageService.showError(
        this.translationService.translate('common.error'),
        'Missing user data for abandon operation'
      );
      return;
    }

    try {
      // Use the first checkpoint for the DNF call (as per backend API)
      const firstCheckpoint = this.checkpoints()[0];
      if (!firstCheckpoint) {
        this.messageService.showError(
          this.translationService.translate('common.error'),
          'No checkpoints available for abandon operation'
        );
        return;
      }

      const url = `${environment.backend_url}randonneur/${currentUser.id}/track/${currentUser.trackuid}/startnumber/${currentUser.startnumber}/checkpoint/${firstCheckpoint.checkpoint_uid}/markasdnf`;

      const response = await firstValueFrom(this.http.put(url, {}));

      // Update local state optimistically
      this.isAbandoned.set(true);

      this.messageService.showSuccess(
        this.translationService.translate('competitor.abandonBrevet'),
        this.translationService.translate('message.brevetAbandoned')
      );

    } catch (error) {
      this.messageService.showError(
        this.translationService.translate('competitor.abandonBrevet'),
        this.translationService.translate('message.abandonFailed')
      );
    }
  }

  async undoAbandonBrevet() {
    const confirmed = confirm(this.translationService.translate('competitor.undoAbandonConfirm'));
    if (!confirmed) return;

    const currentUser = this.currentUser;
    if (!currentUser?.trackuid || !currentUser?.startnumber || !currentUser?.id) {
      this.messageService.showError(
        this.translationService.translate('common.error'),
        'Missing user data for undo abandon operation'
      );
      return;
    }

    try {
      // Use the first checkpoint for the rollback DNF call (as per backend API)
      const firstCheckpoint = this.checkpoints()[0];
      if (!firstCheckpoint) {
        this.messageService.showError(
          this.translationService.translate('common.error'),
          'No checkpoints available for undo abandon operation'
        );
        return;
      }

      const url = `${environment.backend_url}randonneur/${currentUser.id}/track/${currentUser.trackuid}/startnumber/${currentUser.startnumber}/checkpoint/${firstCheckpoint.checkpoint_uid}/rollbackdnf`;

      const response = await firstValueFrom(this.http.put(url, {}));

      // Update local state optimistically
      this.isAbandoned.set(false);

      this.messageService.showSuccess(
        this.translationService.translate('competitor.undoAbandon'),
        this.translationService.translate('message.abandonUndone')
      );

    } catch (error) {
      this.messageService.showError(
        this.translationService.translate('competitor.undoAbandon'),
        this.translationService.translate('message.undoFailed')
      );
    }
  }

  onCheckpointActionCompleted(event: any) {
    // Update the local checkpoint data optimistically
    this.updateCheckpointOptimistically(event.checkpoint, event.action);

    // Force immediate refresh of state data to verify with backend
    this.competitorService.refreshState();
  }

  /**
   * Update checkpoint status optimistically in the parent component
   */
  private updateCheckpointOptimistically(checkpoint: any, action: string) {
    const currentCheckpoints = this.checkpoints();
    const updatedCheckpoints = currentCheckpoints.map(cp => {
      if (cp.checkpoint_uid === checkpoint.checkpoint_uid) {
        let newStatus = cp.status;

        switch (action) {
          case 'start':
          case 'check-in':
            newStatus = 'checked-in';
            break;
          case 'check-out':
            newStatus = 'checked-out';
            break;
          case 'undo-checkout':
            newStatus = 'checked-in';
            break;
          case 'undo-check-in':
            newStatus = 'not-visited';
            break;
        }

        return { ...cp, status: newStatus };
      }
      return cp;
    });

    this.checkpoints.set(updatedCheckpoints);

    // Update progress after checkpoint status change
    this.updateProgress();
  }

  /**
   * Calculate and update cycling progress based on completed checkpoints
   */
  private updateProgress() {
    const checkpoints = this.checkpoints();
    const trackInfo = this.trackInfo();

    if (!checkpoints || checkpoints.length === 0 || !trackInfo?.totalDistanceKm) {
      this.cyclingProgress.set({
        currentDistance: 0,
        totalDistance: 0,
        progressPercentage: 0
      });
      return;
    }

    const totalDistance = trackInfo.totalDistanceKm;

    // Find the farthest checkpoint that has been completed (checked-in or checked-out)
    // Sort checkpoints by distance and find the last completed one
    const sortedCheckpoints = [...checkpoints].sort((a, b) => a.distance - b.distance);
    let farthestCompletedDistance = 0;

    for (const checkpoint of sortedCheckpoints) {
      if (checkpoint.status === 'checked-in' || checkpoint.status === 'checked-out') {
        farthestCompletedDistance = checkpoint.distance;
      } else {
        // Stop at first non-completed checkpoint to ensure sequential progress
        break;
      }
    }

    const progressPercentage = totalDistance > 0 ? (farthestCompletedDistance / totalDistance) * 100 : 0;

    this.cyclingProgress.set({
      currentDistance: farthestCompletedDistance,
      totalDistance: totalDistance,
      progressPercentage: Math.min(progressPercentage, 100) // Cap at 100%
    });
  }

}
