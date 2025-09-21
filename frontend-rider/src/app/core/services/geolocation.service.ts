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

export interface PermissionState {
  status: 'granted' | 'denied' | 'prompt' | 'unknown';
  lastChecked: number;
  isStale: boolean;
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
  private _permissionState = signal<PermissionState>({
    status: 'unknown',
    lastChecked: 0,
    isStale: true
  });
  private _permissionCheckInterval: any = null;
  private _permissionChangeListener: any = null;

  // Read-only signals for components
  readonly isSupported$ = this._isSupported.asReadonly();
  readonly isWatching$ = this._isWatching.asReadonly();
  readonly currentPosition$ = this._currentPosition.asReadonly();
  readonly lastError$ = this._lastError.asReadonly();
  readonly isBackgroundTracking$ = this._isBackgroundTracking.asReadonly();
  readonly geofences$ = this._geofences.asReadonly();
  readonly permissionState$ = this._permissionState.asReadonly();

  // Computed signals
  readonly hasPosition = computed(() => this._currentPosition() !== null);
  readonly isAvailable = computed(() => this._isSupported() && !this._isWatching());
  readonly activeGeofences = computed(() => this._geofences().filter(g => g.isActive));
  readonly hasValidPermission = computed(() => {
    const state = this._permissionState();
    return state.status === 'granted' && !state.isStale;
  });

  private checkGeolocationSupport(): boolean {
    return 'geolocation' in navigator;
  }

  /**
   * Initialize permission monitoring and state management
   */
  initializePermissionMonitoring(): void {
    if (!this._isSupported()) {
      return;
    }

    // Check initial permission state
    this.checkPermissionState();

    // Set up periodic permission checking (every 30 seconds)
    this._permissionCheckInterval = setInterval(() => {
      this.checkPermissionState();
    }, 30000);

    // Listen for permission changes
    this.setupPermissionChangeListener();
  }

  /**
   * Check current permission state and update internal state
   */
  async checkPermissionState(): Promise<PermissionState> {
    if (!this._isSupported()) {
      const state: PermissionState = {
        status: 'unknown',
        lastChecked: Date.now(),
        isStale: false
      };
      this._permissionState.set(state);
      return state;
    }

    try {
      const permission = await navigator.permissions.query({ name: 'geolocation' as PermissionName });
      const now = Date.now();
      const lastChecked = this._permissionState().lastChecked;
      const isStale = now - lastChecked > 300000; // 5 minutes

      const state: PermissionState = {
        status: permission.state as 'granted' | 'denied' | 'prompt',
        lastChecked: now,
        isStale
      };

      this._permissionState.set(state);

      // Update localStorage to keep it in sync
      this.syncPermissionToLocalStorage(state);

      return state;
    } catch (error) {
      const state: PermissionState = {
        status: 'unknown',
        lastChecked: Date.now(),
        isStale: true
      };
      this._permissionState.set(state);
      return state;
    }
  }

  /**
   * Setup listener for permission changes
   */
  private setupPermissionChangeListener(): void {
    if (!this._isSupported() || !navigator.permissions) {
      return;
    }

    try {
      navigator.permissions.query({ name: 'geolocation' as PermissionName }).then(permission => {
        this._permissionChangeListener = () => {
          this.checkPermissionState();
        };
        permission.addEventListener('change', this._permissionChangeListener);
      });
    } catch (error) {
      // Permission API not fully supported
    }
  }

  /**
   * Sync permission state to localStorage
   */
  private syncPermissionToLocalStorage(state: PermissionState): void {
    if (state.status === 'granted') {
      localStorage.setItem('geolocationPermissionGranted', 'true');
    } else if (state.status === 'denied') {
      localStorage.setItem('geolocationPermissionGranted', 'false');
    }
    // Don't update localStorage for 'prompt' or 'unknown' states
  }

  /**
   * Get current permission state
   */
  getCurrentPermissionState(): PermissionState {
    return this._permissionState();
  }

  /**
   * Check if permission is currently granted and valid
   */
  isPermissionGranted(): boolean {
    return this.hasValidPermission();
  }

  /**
   * Request permission with enhanced error handling
   */
  async requestPermissionWithRetry(maxRetries: number = 3): Promise<boolean> {
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
      try {
        const granted = await firstValueFrom(this.requestPermission());
        if (granted) {
          await this.checkPermissionState();
          return true;
        }
      } catch (error) {
        if (attempt === maxRetries) {
          throw error;
        }
        // Wait before retry
        await new Promise(resolve => setTimeout(resolve, 1000 * attempt));
      }
    }
    return false;
  }

  getCurrentPosition(options?: PositionOptions): Observable<Position> {
    if (!this._isSupported()) {
      return throwError(() => this.createError('UNSUPPORTED', 'Geolocation is not supported in this browser'));
    }

    // Note: We don't check permission here as it might not be updated yet
    // The browser will handle permission checking when we call getCurrentPosition

    return from(
      new Promise<Position>((resolve, reject) => {
        const defaultOptions: PositionOptions = {
          maximumAge: 60000, // Allow positions up to 1 minute old
          timeout: 25000, // Increase timeout to 25 seconds
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

            console.error('Geolocation getCurrentPosition error:', geoError);

            // If permission was denied, update our permission state
            if (geoError.type === 'PERMISSION_DENIED') {
              this.checkPermissionState();
            }

            reject(geoError);
          },
          defaultOptions
        );
      })
    ).pipe(
      catchError(error => {
        // Silent fail for geolocation errors
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
            return new Promise<boolean>((resolve) => {
              navigator.geolocation.getCurrentPosition(
                () => resolve(true),
                () => resolve(false),
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
              );
            });
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

  /**
   * Cleanup method to stop all monitoring and clear resources
   */
  cleanup(): void {
    // Stop background tracking
    this.stopBackgroundTracking();

    // Stop permission monitoring
    if (this._permissionCheckInterval) {
      clearInterval(this._permissionCheckInterval);
      this._permissionCheckInterval = null;
    }

    // Remove permission change listener
    if (this._permissionChangeListener) {
      try {
        navigator.permissions.query({ name: 'geolocation' as PermissionName }).then(permission => {
          permission.removeEventListener('change', this._permissionChangeListener);
        });
      } catch (error) {
        // Permission API not available
      }
      this._permissionChangeListener = null;
    }

    // Stop watching position
    this.stopWatching();
  }

  // Background tracking methods
  async startBackgroundTracking(config: BackgroundLocationConfig): Promise<void> {
    if (this._isBackgroundTracking()) {
      return;
    }

    if (!this._isSupported()) {
      throw new Error('Geolocation not supported');
    }

    // Check permission before starting background tracking
    // Use a more lenient check that allows starting if permission is in prompt state
    const permissionState = this.getCurrentPermissionState();
    if (permissionState.status === 'denied') {
      throw new Error('Geolocation permission not granted');
    }

    // If permission is unknown or prompt, we'll check it dynamically during tracking

    try {
      // Clear any corrupted IndexedDB cache first
      await this.clearIndexedDBCache();
      // Request wake lock to keep screen awake
      if (config.wakeLockEnabled && 'wakeLock' in navigator) {
        this._wakeLock = await (navigator as any).wakeLock.request('screen');
        this._wakeLock?.addEventListener('release', () => {
          // Wake lock released
        });
      }

      // Register background sync if supported
      if (config.backgroundSyncEnabled && 'serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
        await this.registerBackgroundSync();
      }

      this._isBackgroundTracking.set(true);

      // Start interval for position updates
      this._backgroundInterval = setInterval(async () => {
        const now = new Date().toLocaleTimeString();
        console.log(`[${now}] Background location tracking - Getting position...`);

        try {
          // Check permission before attempting to get position
          const permissionState = this.getCurrentPermissionState();
          if (permissionState.status === 'denied') {
            console.log(`[${now}] Permission denied, stopping background tracking`);
            this.messageService.showWarning('Background Tracking', 'Geolocation permission lost, stopping background tracking');
            this.stopBackgroundTracking();
            return;
          }

          // Try high accuracy first, fallback to lower accuracy if it times out
          let position;
          try {
            position = await firstValueFrom(this.getCurrentPosition({
              enableHighAccuracy: true,
              timeout: 15000,
              maximumAge: 60000 // Allow older positions for background tracking
            }));
          } catch (error) {
            // If high accuracy fails, try with lower accuracy but longer timeout
            if (error && typeof error === 'object' && 'type' in error && error.type === 'TIMEOUT') {
              console.log(`[${now}] High accuracy timed out, trying lower accuracy...`);
              try {
                position = await firstValueFrom(this.getCurrentPosition({
                  enableHighAccuracy: false,
                  timeout: 30000,
                  maximumAge: 120000 // Allow even older positions as fallback
                }));
                console.log(`[${now}] Fallback positioning successful`);
              } catch (fallbackError) {
                console.warn(`[${now}] Both high and low accuracy failed:`, fallbackError);
                throw fallbackError;
              }
            } else {
              throw error;
            }
          }

          console.log(`[${now}] Background tracking - Position obtained:`, {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            accuracy: position.coords.accuracy
          });

          if (position && config.apiEndpoint) {
           // console.log(`[${now}] Sending position to API:`, config.apiEndpoint);
          //  await this.sendPositionToServer(position, config.apiEndpoint);
           // console.log(`[${now}] Position sent to server successfully`);
          } else {
            console.log(`[${now}] No API endpoint configured, position not sent to server`);
          }
        } catch (error) {
          // Check if it's a permission error
          if (error && typeof error === 'object' && 'type' in error && error.type === 'PERMISSION_DENIED') {
            console.log(`[${now}] Permission error during background tracking`);
            this.messageService.showWarning('Background Tracking', 'Geolocation permission lost, stopping background tracking');
            this.stopBackgroundTracking();
          } else if (error && typeof error === 'object' && 'type' in error && error.type === 'TIMEOUT') {
            // Timeout errors are common - just log and continue
            console.warn(`[${now}] Background tracking timeout - will try again next interval`);
          } else {
            // Other background tracking error - log but continue
            console.warn(`[${now}] Background tracking error (continuing):`, error);
          }
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
      // Position sent to server successfully
    } catch (error) {
      // Failed to send position to server - silent fail
      // Store for retry later
      this.storePositionForRetry(payload);
    }
  }

  private storePositionForRetry(payload: any): void {
    // Store in IndexedDB for retry when online
    if ('indexedDB' in window) {
      const request = indexedDB.open('LocationCache', 1);

      request.onupgradeneeded = () => {
        const db = request.result;
        if (!db.objectStoreNames.contains('positions')) {
          const store = db.createObjectStore('positions', { keyPath: 'id' });
          store.createIndex('timestamp', 'timestamp', { unique: false });
        }
      };

      request.onsuccess = () => {
        const db = request.result;
        try {
          const transaction = db.transaction(['positions'], 'readwrite');
          const store = transaction.objectStore('positions');
          store.add({ ...payload, id: Date.now(), retryCount: 0, timestamp: new Date().toISOString() });
        } catch (error) {
          console.warn('Failed to store position for retry:', error);
        }
      };

      request.onerror = () => {
        console.warn('IndexedDB error:', request.error);
      };
    }
  }

  /**
   * Clear corrupted IndexedDB and start fresh
   */
  private clearIndexedDBCache(): Promise<void> {
    return new Promise((resolve) => {
      if ('indexedDB' in window) {
        const deleteRequest = indexedDB.deleteDatabase('LocationCache');
        deleteRequest.onsuccess = () => {
          console.log('Cleared corrupted IndexedDB cache');
          resolve();
        };
        deleteRequest.onerror = () => {
          console.warn('Failed to clear IndexedDB cache');
          resolve();
        };
      } else {
        resolve();
      }
    });
  }

  // Service Worker registration for background sync
  async registerServiceWorker(): Promise<void> {
    if ('serviceWorker' in navigator) {
      try {
        const registration = await navigator.serviceWorker.register('/sw.js');
        // Service Worker registered successfully
      } catch (error) {
        // Service Worker registration failed - silent fail
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

    // Geofence event detected

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
        // Geofence event sent to API
      } catch (error) {
        // Failed to send geofence event to API
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
        // Background tracking with geofencing error
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
