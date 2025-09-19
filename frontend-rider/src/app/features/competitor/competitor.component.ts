import { Component, inject, signal, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { GeolocationService } from '../../core/services/geolocation.service';
import { MessageService } from '../../core/services/message.service';
import { AuthService } from '../../core/services/auth.service';
import { CompetitorHeaderComponent } from '../../shared/components/competitor-header/competitor-header.component';
import { CheckpointCardComponent, CheckpointData } from '../../shared/components/checkpoint-card/checkpoint-card.component';
import { CompetitorPollingService, CombinedCheckpointData } from '../../core/services/competitor-polling.service';
import { firstValueFrom } from 'rxjs';

@Component({
  selector: 'app-competitor',
  standalone: true,
  imports: [CommonModule, CompetitorHeaderComponent, CheckpointCardComponent],
  templateUrl: './competitor.component.html',
  styleUrl: './competitor.component.scss'
})
export class CompetitorComponent implements OnInit, OnDestroy {
  private router = inject(Router);
  private geolocationService = inject(GeolocationService);
  private messageService = inject(MessageService);
  private authService = inject(AuthService);
  private competitorService = inject(CompetitorPollingService);

  hasGeolocationPermission = signal<boolean | null>(null);
  isCheckingPermission = signal(true);

  // Backend data
  competitorData$ = this.competitorService.combinedData$;
  checkpoints = signal<CheckpointData[]>([]);
  trackInfo = signal<any>(null);
  participantInfo = signal<any>(null);
  participantName = signal<string>('Loading...');

  ngOnInit() {
    this.checkGeolocationPermission();
    this.loadCompetitorData();
  }

  ngOnDestroy() {
    // Stop polling when component is destroyed
    this.competitorService.stopPolling();
  }

  private loadCompetitorData() {
    // Get authenticated user data
    const activeUser = this.authService.getActiveUser();
    const token = localStorage.getItem('loggedInUser');

    console.log('Active user data:', activeUser);
    console.log('Token in localStorage:', token ? 'Present' : 'Missing');
    // Check authentication status
    const isAuthenticated = !!token && !!activeUser;

    if (!activeUser) {
      console.error('No authenticated user found');
      this.messageService.showError('Authentication', 'No user session found. Please login again.');
      this.router.navigate(['/login']);
      return;
    }

    if (!activeUser.trackuid || !activeUser.startnumber) {
      console.error('Missing track or start number data for authenticated user:', {
        trackuid: activeUser.trackuid,
        startnumber: activeUser.startnumber,
        fullUser: activeUser
      });
      this.messageService.showError('Missing Data', 'Unable to load competitor information. User missing track or start number data.');
      return;
    }

    console.log('Loading competitor data for:', {
      trackUid: activeUser.trackuid,
      startNumber: activeUser.startnumber,
      participantId: activeUser.id,
      userName: activeUser.name
    });

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
    this.competitorData$.subscribe({
      next: (data) => {
        console.log('Competitor data subscription received:', data);
        console.log('Data type:', typeof data);
        console.log('Data is null?', data === null);
        console.log('Data is undefined?', data === undefined);

        if (data) {
          console.log('Processing competitor data from backend:', data);
          console.log('Track data:', data.track);
          console.log('Checkpoints data:', data.checkpoints);

          // Update track info
          this.trackInfo.set({
            title: data.track.title,
            distance: data.track.distance + ' km'
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
                logoFileName: cp.site?.image ? cp.site.image.replace('/api/uploads/', '') : undefined,
                status: cp.status || 'not-visited',
                timestamp: cp.timestamp,
                checkoutTimestamp: cp.checkoutTimestamp,
                isFirst: index === 0,
                isLast: index === data.checkpoints.length - 1
              };
            });
            this.checkpoints.set(transformedCheckpoints);
          } else {
            // Handle case when track has no checkpoints
            console.warn('Track has no checkpoints:', data);
            this.checkpoints.set([]);
            this.messageService.showInfo('Checkpoints Coming Soon', 'Checkpoints for this track will be available soon.');
          }

              // Update participant info
              this.participantInfo.set({
                status: data.participantStatus,
                uid: data.participantUid,
                lastUpdated: data.lastUpdated
              });

              // Update participant name
              this.participantName.set(data.participantName || 'Rider #' + (this.currentUser?.startnumber || '000'));
        } else {
          console.log('No data received from competitor service');
        }
      },
      error: (error) => {
        console.error('Error in competitor data subscription:', error);
        this.messageService.showError('Data Error', 'Failed to load competitor data');
      }
    });
  }

  // Get current user data for templates
  get currentUser() {
    return this.authService.getActiveUser();
  }

  // Get location status for header indicator
  getLocationStatus(): 'granted' | 'denied' | 'unknown' {
    const permissionGranted = localStorage.getItem('geolocationPermissionGranted');
    const justGranted = localStorage.getItem('geolocationJustGranted');

    if (permissionGranted === 'true' || justGranted === 'true') {
      return 'granted';
    }

    if (permissionGranted === 'false') {
      return 'denied';
    }

    return 'unknown';
  }

  // Get participant name from backend data
  getParticipantName(): string {
    return this.participantName();
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

      // Check if we have stored permission state
      const permissionGranted = localStorage.getItem('geolocationPermissionGranted');
      const justGranted = localStorage.getItem('geolocationJustGranted');

      if (permissionGranted === 'true') {
        console.log('Permission previously granted, showing dashboard');
        this.hasGeolocationPermission.set(true);

        if (justGranted === 'true') {
          console.log('User just granted permission');
          localStorage.removeItem('geolocationJustGranted');
        } else {
          console.log('Using previously stored permission');
        }

        this.isCheckingPermission.set(false);
        return;
      }

      if (justGranted === 'true') {
        console.log('User just granted permission, skipping permission check redirect');
        localStorage.removeItem('geolocationJustGranted');
        this.hasGeolocationPermission.set(true);
        this.isCheckingPermission.set(false);
        return;
      }

      // Check current permission status
      const permission = await navigator.permissions.query({ name: 'geolocation' as PermissionName });
      console.log('Current geolocation permission state:', permission.state);

          if (permission.state === 'granted') {
            this.hasGeolocationPermission.set(true);
            console.log('Permission already granted, showing dashboard');

            // Try to get current position to verify it actually works
            try {
              const position = await firstValueFrom(this.geolocationService.getCurrentPosition());
              console.log('Location verified successfully:', position);
            } catch (error) {
              console.warn('Failed to get current position despite permission granted:', error);
            }
          } else if (permission.state === 'denied') {
            this.hasGeolocationPermission.set(false);
            console.log('Permission denied, redirecting to permission page');
            // Only redirect if not coming from permission page
            setTimeout(() => {
              this.router.navigate(['/geolocation-permission']);
            }, 100);
          } else {
            // Permission is 'prompt' - first time or reset
            console.log('Permission is prompt (first time or reset), redirecting to permission page');
            console.log('Current URL:', window.location.href);
            // Only redirect if not already on permission page
            if (!window.location.pathname.includes('geolocation-permission')) {
              setTimeout(() => {
                this.router.navigate(['/geolocation-permission']);
              }, 100);
            }
          }
    } catch (error) {
      console.error('Error checking geolocation permission:', error);
      this.hasGeolocationPermission.set(false);
      this.router.navigate(['/geolocation-permission']);
    } finally {
      this.isCheckingPermission.set(false);
    }
  }

  requestLocationPermission() {
    this.router.navigate(['/geolocation-permission']);
  }

  logout() {
    console.log('Logging out...');
    this.authService.logout();
    this.router.navigate(['/login']);
  }

  abandonBrevet() {
    console.log('Abandoning brevet...');
    // TODO: Implement abandon brevet logic
    this.messageService.showWarning('Abandon Brevet', 'Brevet abandonment functionality will be implemented soon');
  }

  onCheckpointActionCompleted(event: any) {
    console.log('Checkpoint action completed:', event);

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
    console.log(`Parent optimistic update: ${checkpoint.name} -> ${action}`);
  }

}
