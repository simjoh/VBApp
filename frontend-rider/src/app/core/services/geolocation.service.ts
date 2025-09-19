import { Injectable, signal, computed, inject } from '@angular/core';
import { Observable, from, throwError, interval, timer, firstValueFrom } from 'rxjs';
import { catchError, map, switchMap, takeUntil, tap } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';
import { MessageService } from './message.service';

export interface Position {
  coords: {
    latitude: number;
    longitude: number;
    altitude: number | null;
    accuracy: number;
    altitudeAccuracy: number | null;
    heading: number | null;
    speed: number | null;
  };
  timestamp: number;
}

export interface GeolocationError {
  code: number;
  message: string;
  type: 'PERMISSION_DENIED' | 'POSITION_UNAVAILABLE' | 'TIMEOUT' | 'UNSUPPORTED';
}

export interface BackgroundLocationConfig {
  enabled: boolean;
  intervalMinutes: number;
  apiEndpoint?: string;
  wakeLockEnabled: boolean;
  backgroundSyncEnabled: boolean;
}

export interface Geofence {
  id: string;
  name: string;
  latitude: number;
  longitude: number;
  radius: number; // in meters
  enterCallback?: (geofence: Geofence, position: Position) => void;
  exitCallback?: (geofence: Geofence, position: Position) => void;
  enterApiEndpoint?: string;
  exitApiEndpoint?: string;
  isActive: boolean;
}

export interface GeofenceEvent {
  geofenceId: string;
  eventType: 'enter' | 'exit';
  position: Position;
  timestamp: number;
  distance: number;
}

@Injectable({
  providedIn: 'root'
})
export class GeolocationService {
  private httpClient = inject(HttpClient);
  private messageService = inject(MessageService);

  private _isSupported = signal<boolean>(this.checkGeolocationSupport());
  private _isWatching = signal<boolean>(false);
  private _currentPosition = signal<Position | null>(null);
  private _lastError = signal<GeolocationError | null>(null);
  private _isBackgroundTracking = signal<boolean>(false);
  private _wakeLock: WakeLockSentinel | null = null;
  private _watchId: number | null = null;
  private _backgroundInterval: any = null;
  private _geofences = signal<Geofence[]>([]);
  private _geofenceStates = new Map<string, boolean>(); // Track if inside geofence
  private _lastKnownPosition: Position | null = null;

  // Read-only signals for components
  readonly isSupported$ = this._isSupported.asReadonly();
  readonly isWatching$ = this._isWatching.asReadonly();
  readonly currentPosition$ = this._currentPosition.asReadonly();
  readonly lastError$ = this._lastError.asReadonly();
  readonly isBackgroundTracking$ = this._isBackgroundTracking.asReadonly();
  readonly geofences$ = this._geofences.asReadonly();

  // Computed signals
  readonly hasPosition = computed(() => this._currentPosition() !== null);
  readonly isAvailable = computed(() => this._isSupported() && !this._isWatching());
  readonly activeGeofences = computed(() => this._geofences().filter(g => g.isActive));

  private checkGeolocationSupport(): boolean {
    return 'geolocation' in navigator;
  }

  getCurrentPosition(options?: PositionOptions): Observable<Position> {
    if (!this._isSupported()) {
      return throwError(() => this.createError('UNSUPPORTED', 'Geolocation is not supported in this browser'));
    }

    return from(
      new Promise<Position>((resolve, reject) => {
        const defaultOptions: PositionOptions = {
          maximumAge: 10000,
          timeout: 15000,
          enableHighAccuracy: true,
          ...options
        };

        navigator.geolocation.getCurrentPosition(
          (position) => {
            const pos: Position = {
              coords: {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                altitude: position.coords.altitude,
                accuracy: position.coords.accuracy,
                altitudeAccuracy: position.coords.altitudeAccuracy,
                heading: position.coords.heading,
                speed: position.coords.speed
              },
              timestamp: position.timestamp
            };
            this._currentPosition.set(pos);
            this._lastError.set(null);
            resolve(pos);
          },
          (error) => {
            const geoError = this.mapGeolocationError(error);
            this._lastError.set(geoError);
            reject(geoError);
          },
          defaultOptions
        );
      })
    ).pipe(
      catchError(error => {
        console.error('Geolocation error:', error);
        return throwError(() => error);
      })
    );
  }

  watchPosition(options?: PositionOptions): Observable<Position> {
    if (!this._isSupported()) {
      return throwError(() => this.createError('UNSUPPORTED', 'Geolocation is not supported in this browser'));
    }

    if (this._isWatching()) {
      return throwError(() => this.createError('POSITION_UNAVAILABLE', 'Already watching position'));
    }

    return new Observable<Position>((observer) => {
      const defaultOptions: PositionOptions = {
        maximumAge: 5000,
        timeout: 10000,
        enableHighAccuracy: true,
        ...options
      };

      this._isWatching.set(true);
      this._lastError.set(null);

      this._watchId = navigator.geolocation.watchPosition(
        (position) => {
          const pos: Position = {
            coords: {
              latitude: position.coords.latitude,
              longitude: position.coords.longitude,
              altitude: position.coords.altitude,
              accuracy: position.coords.accuracy,
              altitudeAccuracy: position.coords.altitudeAccuracy,
              heading: position.coords.heading,
              speed: position.coords.speed
            },
            timestamp: position.timestamp
          };
          this._currentPosition.set(pos);
          this._lastError.set(null);
          observer.next(pos);
        },
        (error) => {
          const geoError = this.mapGeolocationError(error);
          this._lastError.set(geoError);
          observer.error(geoError);
        },
        defaultOptions
      );

      // Cleanup on unsubscribe
      return () => {
        this.stopWatching();
      };
    });
  }

  stopWatching(): void {
    if (this._watchId !== null) {
      navigator.geolocation.clearWatch(this._watchId);
      this._watchId = null;
      this._isWatching.set(false);
    }
  }

  clearPosition(): void {
    this._currentPosition.set(null);
    this._lastError.set(null);
  }

  requestPermission(): Observable<boolean> {
    if (!this._isSupported()) {
      return throwError(() => this.createError('UNSUPPORTED', 'Geolocation is not supported'));
    }

    return from(
      navigator.permissions.query({ name: 'geolocation' as PermissionName })
        .then(permission => {
          if (permission.state === 'granted') {
            return true;
          } else if (permission.state === 'prompt') {
            // Try to get position to trigger permission prompt
            return firstValueFrom(this.getCurrentPosition().pipe(
              map(() => true),
              catchError(() => from([false]))
            )).then(result => result ?? false);
          } else {
            return false;
          }
        })
    );
  }

  private mapGeolocationError(error: GeolocationPositionError): GeolocationError {
    let type: GeolocationError['type'];
    let message: string;

    switch (error.code) {
      case error.PERMISSION_DENIED:
        type = 'PERMISSION_DENIED';
        message = 'Location access denied by user';
        break;
      case error.POSITION_UNAVAILABLE:
        type = 'POSITION_UNAVAILABLE';
        message = 'Location information is unavailable';
        break;
      case error.TIMEOUT:
        type = 'TIMEOUT';
        message = 'Location request timed out';
        break;
      default:
        type = 'POSITION_UNAVAILABLE';
        message = 'Unknown geolocation error';
    }

    return {
      code: error.code,
      message,
      type
    };
  }

  private createError(type: GeolocationError['type'], message: string): GeolocationError {
    return {
      code: -1,
      message,
      type
    };
  }

  // Utility methods
  getDistanceFromCurrentPosition(lat: number, lng: number): number | null {
    const current = this._currentPosition();
    if (!current) return null;

    return this.calculateDistance(
      current.coords.latitude,
      current.coords.longitude,
      lat,
      lng
    );
  }

  private calculateDistance(lat1: number, lng1: number, lat2: number, lng2: number): number {
    const R = 6371; // Earth's radius in kilometers
    const dLat = this.toRadians(lat2 - lat1);
    const dLng = this.toRadians(lng2 - lng1);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(this.toRadians(lat1)) * Math.cos(this.toRadians(lat2)) *
      Math.sin(dLng / 2) * Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }

  private toRadians(degrees: number): number {
    return degrees * (Math.PI / 180);
  }

  // Background tracking methods
  async startBackgroundTracking(config: BackgroundLocationConfig): Promise<void> {
    if (this._isBackgroundTracking()) {
      this.messageService.showWarning('Background Tracking', 'Already running background tracking');
      return;
    }

    if (!this._isSupported()) {
      throw new Error('Geolocation not supported');
    }

    try {
      // Request wake lock to keep screen awake
      if (config.wakeLockEnabled && 'wakeLock' in navigator) {
        this._wakeLock = await (navigator as any).wakeLock.request('screen');
        this._wakeLock?.addEventListener('release', () => {
          console.log('Wake lock released');
        });
      }

      // Register background sync if supported
      if (config.backgroundSyncEnabled && 'serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
        await this.registerBackgroundSync();
      }

      this._isBackgroundTracking.set(true);
      this.messageService.showSuccess('Background Tracking', 'Started background location tracking');

      // Start interval for position updates
      this._backgroundInterval = setInterval(async () => {
        try {
          const position = await firstValueFrom(this.getCurrentPosition({
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 30000
          }));

          if (position && config.apiEndpoint) {
            await this.sendPositionToServer(position, config.apiEndpoint);
          }
        } catch (error) {
          console.error('Background tracking error:', error);
          this.messageService.showError('Background Tracking', 'Failed to get location');
        }
      }, config.intervalMinutes * 60 * 1000);

      // Get initial position
      this.getCurrentPosition().subscribe();

    } catch (error) {
      this.messageService.showError('Background Tracking', 'Failed to start background tracking');
      throw error;
    }
  }

  stopBackgroundTracking(): void {
    if (!this._isBackgroundTracking()) {
      return;
    }

    // Clear interval
    if (this._backgroundInterval) {
      clearInterval(this._backgroundInterval);
      this._backgroundInterval = null;
    }

    // Release wake lock
    if (this._wakeLock) {
      this._wakeLock.release();
      this._wakeLock = null;
    }

    // Stop watching
    this.stopWatching();

    this._isBackgroundTracking.set(false);
    this.messageService.showInfo('Background Tracking', 'Stopped background location tracking');
  }

  private async registerBackgroundSync(): Promise<void> {
    if ('serviceWorker' in navigator) {
      const registration = await navigator.serviceWorker.ready;
      await (registration as any).sync.register('background-location-sync');
    }
  }

  private async sendPositionToServer(position: Position, endpoint: string): Promise<void> {
    const payload = {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude,
      accuracy: position.coords.accuracy,
      timestamp: position.timestamp,
      altitude: position.coords.altitude,
      speed: position.coords.speed,
      heading: position.coords.heading
    };

    try {
      await firstValueFrom(this.httpClient.post(endpoint, payload));
      console.log('Position sent to server:', payload);
    } catch (error) {
      console.error('Failed to send position to server:', error);
      // Store for retry later
      this.storePositionForRetry(payload);
    }
  }

  private storePositionForRetry(payload: any): void {
    // Store in IndexedDB for retry when online
    if ('indexedDB' in window) {
      const request = indexedDB.open('LocationCache', 1);
      request.onsuccess = () => {
        const db = request.result;
        const transaction = db.transaction(['positions'], 'readwrite');
        const store = transaction.objectStore('positions');
        store.add({ ...payload, id: Date.now(), retryCount: 0 });
      };
    }
  }

  // Service Worker registration for background sync
  async registerServiceWorker(): Promise<void> {
    if ('serviceWorker' in navigator) {
      try {
        const registration = await navigator.serviceWorker.register('/sw.js');
        console.log('Service Worker registered:', registration);
      } catch (error) {
        console.error('Service Worker registration failed:', error);
      }
    }
  }

  // Geofencing methods
  addGeofence(geofence: Geofence): void {
    const existingGeofences = this._geofences();
    const geofenceExists = existingGeofences.some(g => g.id === geofence.id);

    if (geofenceExists) {
      this.messageService.showWarning('Geofence', `Geofence ${geofence.name} already exists`);
      return;
    }

    this._geofences.set([...existingGeofences, geofence]);
    this._geofenceStates.set(geofence.id, false);
    this.messageService.showSuccess('Geofence', `Added geofence: ${geofence.name}`);
  }

  removeGeofence(geofenceId: string): void {
    const existingGeofences = this._geofences();
    const geofence = existingGeofences.find(g => g.id === geofenceId);

    if (geofence) {
      this._geofences.set(existingGeofences.filter(g => g.id !== geofenceId));
      this._geofenceStates.delete(geofenceId);
      this.messageService.showInfo('Geofence', `Removed geofence: ${geofence.name}`);
    }
  }

  updateGeofence(geofenceId: string, updates: Partial<Geofence>): void {
    const existingGeofences = this._geofences();
    const geofenceIndex = existingGeofences.findIndex(g => g.id === geofenceId);

    if (geofenceIndex !== -1) {
      const updatedGeofences = [...existingGeofences];
      updatedGeofences[geofenceIndex] = { ...updatedGeofences[geofenceIndex], ...updates };
      this._geofences.set(updatedGeofences);
      this.messageService.showInfo('Geofence', `Updated geofence: ${updates.name || geofenceId}`);
    }
  }

  toggleGeofence(geofenceId: string): void {
    const existingGeofences = this._geofences();
    const geofenceIndex = existingGeofences.findIndex(g => g.id === geofenceId);

    if (geofenceIndex !== -1) {
      const updatedGeofences = [...existingGeofences];
      updatedGeofences[geofenceIndex].isActive = !updatedGeofences[geofenceIndex].isActive;
      this._geofences.set(updatedGeofences);

      const status = updatedGeofences[geofenceIndex].isActive ? 'activated' : 'deactivated';
      this.messageService.showInfo('Geofence', `${updatedGeofences[geofenceIndex].name} ${status}`);
    }
  }

  checkGeofences(position: Position): void {
    const activeGeofences = this.activeGeofences();

    for (const geofence of activeGeofences) {
      const distance = this.calculateDistance(
        position.coords.latitude,
        position.coords.longitude,
        geofence.latitude,
        geofence.longitude
      ) * 1000; // Convert to meters

      const isInside = distance <= geofence.radius;
      const wasInside = this._geofenceStates.get(geofence.id) || false;

      // Enter geofence
      if (isInside && !wasInside) {
        this.handleGeofenceEvent(geofence, position, 'enter', distance);
        this._geofenceStates.set(geofence.id, true);
      }
      // Exit geofence
      else if (!isInside && wasInside) {
        this.handleGeofenceEvent(geofence, position, 'exit', distance);
        this._geofenceStates.set(geofence.id, false);
      }
    }
  }

  private async handleGeofenceEvent(
    geofence: Geofence,
    position: Position,
    eventType: 'enter' | 'exit',
    distance: number
  ): Promise<void> {
    const event: GeofenceEvent = {
      geofenceId: geofence.id,
      eventType,
      position,
      timestamp: Date.now(),
      distance
    };

    console.log(`Geofence ${eventType}:`, {
      geofence: geofence.name,
      distance: Math.round(distance),
      position: `${position.coords.latitude}, ${position.coords.longitude}`
    });

    // Show notification
    const message = `${eventType === 'enter' ? 'Entered' : 'Exited'} ${geofence.name} (${Math.round(distance)}m away)`;
    this.messageService.showInfo('Geofence', message);

    // Call custom callback
    if (eventType === 'enter' && geofence.enterCallback) {
      geofence.enterCallback(geofence, position);
    } else if (eventType === 'exit' && geofence.exitCallback) {
      geofence.exitCallback(geofence, position);
    }

    // Send to API endpoint
    const apiEndpoint = eventType === 'enter' ? geofence.enterApiEndpoint : geofence.exitApiEndpoint;
    if (apiEndpoint) {
      try {
        await firstValueFrom(this.httpClient.post(apiEndpoint, event));
        console.log(`Geofence ${eventType} sent to API:`, apiEndpoint);
      } catch (error) {
        console.error(`Failed to send geofence ${eventType} to API:`, error);
        this.messageService.showError('Geofence', `Failed to send ${eventType} event`);
      }
    }
  }

  // Enhanced background tracking with geofencing
  async startBackgroundTrackingWithGeofencing(config: BackgroundLocationConfig): Promise<void> {
    await this.startBackgroundTracking(config);

    // Override the interval to include geofence checking
    if (this._backgroundInterval) {
      clearInterval(this._backgroundInterval);
    }

    this._backgroundInterval = setInterval(async () => {
      try {
        const position = await firstValueFrom(this.getCurrentPosition({
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 30000
        }));

        if (position) {
          // Check geofences
          this.checkGeofences(position);
          this._lastKnownPosition = position;

          // Send to server if configured
          if (config.apiEndpoint) {
            await this.sendPositionToServer(position, config.apiEndpoint);
          }
        }
      } catch (error) {
        console.error('Background tracking with geofencing error:', error);
        this.messageService.showError('Background Tracking', 'Failed to get location');
      }
    }, config.intervalMinutes * 60 * 1000);
  }

  // Get geofences near current position
  getNearbyGeofences(radiusKm: number = 5): Geofence[] {
    const currentPosition = this._currentPosition();
    if (!currentPosition) return [];

    return this.activeGeofences().filter(geofence => {
      const distance = this.calculateDistance(
        currentPosition.coords.latitude,
        currentPosition.coords.longitude,
        geofence.latitude,
        geofence.longitude
      );
      return distance <= radiusKm;
    });
  }

  // Check if currently inside any geofence
  isInsideAnyGeofence(): boolean {
    return Array.from(this._geofenceStates.values()).some(isInside => isInside);
  }

  // Get current geofence status
  getGeofenceStatus(): { geofenceId: string; isInside: boolean; distance?: number }[] {
    const currentPosition = this._currentPosition();
    if (!currentPosition) return [];

    return this.activeGeofences().map(geofence => {
      const distance = this.calculateDistance(
        currentPosition.coords.latitude,
        currentPosition.coords.longitude,
        geofence.latitude,
        geofence.longitude
      ) * 1000; // Convert to meters

      return {
        geofenceId: geofence.id,
        isInside: distance <= geofence.radius,
        distance: Math.round(distance)
      };
    });
  }
}
