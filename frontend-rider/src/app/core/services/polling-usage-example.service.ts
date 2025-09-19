import { Injectable, inject, Component, OnInit, OnDestroy, effect } from '@angular/core';
import { CommonModule, AsyncPipe, DatePipe, JsonPipe } from '@angular/common';
import { CompetitorPollingService } from './competitor-polling.service';
import { VisibilityAwarePollingService } from './visibility-aware-polling.service';

/**
 * EXAMPLE: How to use the visibility-aware polling infrastructure
 *
 * This file shows different usage patterns for the polling services.
 * You can copy these patterns into your actual components.
 */

// ========================================
// EXAMPLE 1: Using CompetitorPollingService (Recommended for competitor data)
// ========================================

@Component({
  selector: 'app-competitor-example',
  standalone: true,
  imports: [CommonModule, AsyncPipe, DatePipe],
  template: `
    <div class="competitor-data">
      @if (competitorService.getLoadingState()()) {
        <div class="loading">Loading competitor data...</div>
      }

      @if (competitorService.getErrorState()()) {
        <div class="error">{{ competitorService.getErrorState()() }}</div>
        <button (click)="refreshData()">Retry</button>
      }

      @if (combinedData$ | async; as data) {
        <div class="track-info">
          <h2>{{ data.track.title }}</h2>
          <p>Distance: {{ data.track.distance }}</p>
          <p>Status: {{ data.participantStatus }}</p>
          <p>Last Updated: {{ data.lastUpdated | date:'medium' }}</p>
        </div>

        <div class="checkpoints">
          @for (checkpoint of data.checkpoints; track checkpoint.checkpoint_uid) {
            <div class="checkpoint" [class]="'status-' + checkpoint.status">
              <h3>{{ checkpoint.name }}</h3>
              <p>Status: {{ checkpoint.status }}</p>
              @if (checkpoint.timestamp) {
                <p>Time: {{ checkpoint.timestamp | date:'short' }}</p>
              }
            </div>
          }
        </div>
      }

      <!-- Manual refresh button -->
      <button (click)="refreshData()" class="refresh-btn">
        <i class="pi pi-refresh"></i>
        Refresh
      </button>

      <!-- Polling status indicator -->
      <div class="polling-status">
        App Visible: {{ competitorService.getVisibilityState() ? 'Yes' : 'No' }}
        | Polling: {{ (competitorService.getPollingState())().isPolling ? 'Active' : 'Stopped' }}
      </div>
    </div>
  `
})
export class CompetitorExampleComponent implements OnInit, OnDestroy {
  competitorService = inject(CompetitorPollingService);

  // Subscribe to combined data
  combinedData$ = this.competitorService.combinedData$;

  ngOnInit() {
    // Start polling for competitor data
    // This handles both track data loading and state polling
    this.competitorService.startCompetitorPolling('track-uid-123', '42', {
      interval: 30000,        // 30 seconds
      immediateOnWakeup: true,
      progressiveSlowdown: true,
      maxInterval: 120000     // 2 minutes max
    });
  }

  ngOnDestroy() {
    // Clean up polling
    this.competitorService.stopPolling();
  }

  refreshData() {
    this.competitorService.refreshState();
  }
}

// ========================================
// EXAMPLE 2: Using VisibilityAwarePollingService directly (For custom endpoints)
// ========================================

@Component({
  selector: 'app-custom-polling-example',
  standalone: true,
  imports: [CommonModule, JsonPipe],
  template: `
    <div class="custom-data">
      <h2>Custom API Data</h2>

      @if (customData) {
        <pre>{{ customData | json }}</pre>
      }

      @if (weatherData) {
        <div class="weather">
          <h3>Weather Update</h3>
          <p>{{ weatherData.description }}</p>
        </div>
      }
    </div>
  `
})
export class CustomPollingExampleComponent implements OnInit, OnDestroy {
  private pollingService = inject(VisibilityAwarePollingService);

  customData: any = null;
  weatherData: any = null;

  ngOnInit() {
    // Example 1: Poll custom endpoint every 45 seconds
    this.pollingService.startPolling<any>(
      'custom-api',
      'https://api.example.com/custom-data',
      {
        interval: 45000,
        immediateOnWakeup: true,
        onError: (error) => console.error('Custom API error:', error)
      }
    ).subscribe(data => {
      this.customData = data;
    });

    // Example 2: Poll weather data every 5 minutes with progressive slowdown
    this.pollingService.startPolling<any>(
      'weather-api',
      'https://api.weather.com/current',
      {
        interval: 300000,      // 5 minutes
        progressiveSlowdown: true,
        maxInterval: 1800000,  // 30 minutes max
        immediateOnWakeup: false // Don't fetch immediately on wake for weather
      }
    ).subscribe(data => {
      this.weatherData = data;
    });
  }

  ngOnDestroy() {
    // Stop specific polls
    this.pollingService.stopPolling('custom-api');
    this.pollingService.stopPolling('weather-api');

    // Or stop all polls
    // this.pollingService.stopAllPolling();
  }
}

// ========================================
// EXAMPLE 3: Injectable service for other data (reusable pattern)
// ========================================

@Injectable({
  providedIn: 'root'
})
export class CustomDataPollingService {
  private pollingService = inject(VisibilityAwarePollingService);

  startNotificationsPolling(userId: string) {
    return this.pollingService.startPolling<any[]>(
      'notifications',
      `https://api.example.com/users/${userId}/notifications`,
      {
        interval: 60000, // 1 minute
        immediateOnWakeup: true,
        progressiveSlowdown: false // Keep checking for notifications regularly
      }
    );
  }

  startLiveUpdatesPolling(eventId: string) {
    return this.pollingService.startPolling<any>(
      'live-updates',
      `https://api.example.com/events/${eventId}/live`,
      {
        interval: 15000, // 15 seconds for live updates
        immediateOnWakeup: true,
        progressiveSlowdown: false
      }
    );
  }

  stopNotificationsPolling() {
    this.pollingService.stopPolling('notifications');
  }

  stopLiveUpdatesPolling() {
    this.pollingService.stopPolling('live-updates');
  }
}

// ========================================
// EXAMPLE 4: Monitoring visibility changes
// ========================================

@Component({
  selector: 'app-visibility-monitor',
  standalone: true,
  imports: [CommonModule, DatePipe],
  template: `
    <div class="visibility-status">
      <h3>App Status Monitor</h3>
      <p>Currently: {{ isVisible ? 'Visible' : 'Hidden' }}</p>
      <p>Polling State: {{ pollingState.isPolling ? 'Active' : 'Stopped' }}</p>
      <p>Last Poll: {{ pollingState.lastPollTime | date:'medium' }}</p>
      <p>Error Count: {{ pollingState.errorCount }}</p>
    </div>
  `
})
export class VisibilityMonitorComponent implements OnInit {
  private pollingService = inject(VisibilityAwarePollingService);

  isVisible = true;
  pollingState = this.pollingService.getPollingState()();

  constructor() {
    // Monitor polling state changes using effect (proper signal pattern)
    effect(() => {
      this.pollingState = this.pollingService.getPollingState()();
      console.log('Polling state changed:', this.pollingState);
    });
  }

  ngOnInit() {
    // Monitor visibility changes (this returns an Observable, so subscribe is correct)
    this.pollingService.getVisibilityChanges().subscribe(isVisible => {
      this.isVisible = isVisible;
      console.log(`App visibility changed: ${isVisible ? 'visible' : 'hidden'}`);
    });
  }
}

/**
 * USAGE SUMMARY:
 *
 * 1. For competitor data: Use CompetitorPollingService
 *    - Handles both track data and state polling
 *    - Combines data automatically
 *    - Built-in error handling and loading states
 *
 * 2. For custom endpoints: Use VisibilityAwarePollingService directly
 *    - Full control over polling configuration
 *    - Support for multiple concurrent polls
 *    - Custom error handling
 *
 * 3. Key benefits:
 *    - Automatic pause/resume based on app visibility
 *    - Immediate refresh when app wakes up
 *    - Progressive slowdown to save battery
 *    - Easy cleanup and management
 *
 * 4. Battery optimization features:
 *    - No polling when app is hidden/backgrounded
 *    - Progressive intervals for unchanging data
 *    - Configurable maximum intervals
 *    - Automatic error backoff
 */
