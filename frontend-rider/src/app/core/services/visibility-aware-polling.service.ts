import { Injectable, signal, inject } from '@angular/core';
import { Observable, Subject, fromEvent, merge, timer, switchMap, tap, EMPTY, takeUntil, startWith } from 'rxjs';
import { HttpClient } from '@angular/common/http';

export interface PollingConfig {
  interval: number; // milliseconds
  immediateOnWakeup: boolean;
  progressiveSlowdown: boolean;
  maxInterval?: number;
  onError?: (error: any) => void;
}

export interface PollingState {
  isPolling: boolean;
  isVisible: boolean;
  lastPollTime: Date | null;
  currentInterval: number;
  errorCount: number;
}

@Injectable({
  providedIn: 'root'
})
export class VisibilityAwarePollingService {
  private http = inject(HttpClient);

  // App visibility state
  private isAppVisible = signal(true);
  private visibilityChange$ = new Subject<boolean>();

  // Polling state
  private pollingState = signal<PollingState>({
    isPolling: false,
    isVisible: true,
    lastPollTime: null,
    currentInterval: 30000,
    errorCount: 0
  });

  // Cleanup subjects for each polling instance
  private activePolls = new Map<string, Subject<void>>();

  constructor() {
    // Service initialization
  }

  /**
   * Setup visibility change listeners for app wake/sleep detection
   */
  private setupVisibilityListeners(): void {
    // Visibility polling temporarily disabled
    return;

    // Document visibility API (works on most browsers)
    const visibilityChange$ = fromEvent(document, 'visibilitychange').pipe(
      startWith(null), // Emit initial state
      tap(() => {
        const isVisible = !document.hidden;
        this.updateVisibility(isVisible);
      })
    );

    // Window focus/blur events (iOS Safari fallback)
    const focus$ = fromEvent(window, 'focus').pipe(
      tap(() => this.updateVisibility(true))
    );

    const blur$ = fromEvent(window, 'blur').pipe(
      tap(() => this.updateVisibility(false))
    );

    // Page show/hide events (additional browser support)
    const pageShow$ = fromEvent(window, 'pageshow').pipe(
      tap(() => this.updateVisibility(true))
    );

    const pageHide$ = fromEvent(window, 'pagehide').pipe(
      tap(() => this.updateVisibility(false))
    );

    // Combine all visibility events
    merge(visibilityChange$, focus$, blur$, pageShow$, pageHide$).subscribe();
  }

  /**
   * Update app visibility state and notify subscribers
   */
  private updateVisibility(isVisible: boolean): void {
    // Temporarily disable all logging to fix white screen issue
    // console.log(`[VisibilityPolling] App ${isVisible ? 'visible' : 'hidden'}`);

    this.isAppVisible.set(isVisible);
    this.pollingState.update(state => ({
      ...state,
      isVisible
    }));

    this.visibilityChange$.next(isVisible);
  }

  /**
   * Start visibility-aware polling for a specific endpoint
   * @param pollId Unique identifier for this polling instance
   * @param url API endpoint to poll
   * @param config Polling configuration
   * @returns Observable that emits the polling data
   */
  startPolling<T>(
    pollId: string,
    url: string,
    config: Partial<PollingConfig> = {}
  ): Observable<T> {
    // Simplified polling without visibility detection

    // Default configuration
    const defaultConfig: PollingConfig = {
      interval: 30000, // 30 seconds
      immediateOnWakeup: true,
      progressiveSlowdown: false,
      maxInterval: 120000, // 2 minutes max
      onError: (error) => {
        // Silent fail for polling errors
      }
    };

    const finalConfig = { ...defaultConfig, ...config };


    // Create cleanup subject for this polling instance
    const cleanup$ = new Subject<void>();
    this.activePolls.set(pollId, cleanup$);

    // Update polling state
    this.pollingState.update(state => ({
      ...state,
      isPolling: true,
      currentInterval: finalConfig.interval
    }));

    return this.createPollingStream<T>(url, finalConfig, cleanup$);
  }

  /**
   * Create the actual polling stream with visibility awareness
   */
  private createPollingStream<T>(
    url: string,
    config: PollingConfig,
    cleanup$: Subject<void>
  ): Observable<T> {
    let currentInterval = config.interval;
    let consecutiveErrors = 0;
    let noChangeCount = 0;

    return merge(
      // Initial poll (immediate)
      timer(0),
      // Regular interval polling
      timer(config.interval, config.interval),
      // Immediate poll on app wake up
      this.visibilityChange$.pipe(
        switchMap(isVisible => {
          if (isVisible && config.immediateOnWakeup) {
            return timer(0);
          }
          return EMPTY;
        })
      )
    ).pipe(
      // Only poll when app is visible
      switchMap(() => {
        if (!this.isAppVisible()) {
          return EMPTY;
        }

        return this.http.get<T>(url).pipe(
          tap((data) => {
            // Reset error count on success
            consecutiveErrors = 0;

            // Update last poll time
            this.pollingState.update(state => ({
              ...state,
              lastPollTime: new Date(),
              errorCount: 0
            }));

            // Progressive slowdown logic
            if (config.progressiveSlowdown) {
              noChangeCount++;
              currentInterval = this.calculateProgressiveInterval(
                config.interval,
                noChangeCount,
                config.maxInterval || config.interval * 4
              );
            }
          }),
          // Handle errors
          tap({
            error: (error) => {
              consecutiveErrors++;
              this.pollingState.update(state => ({
                ...state,
                errorCount: consecutiveErrors
              }));

              if (config.onError) {
                config.onError(error);
              }
            }
          })
        );
      }),
      // Continue until cleanup
      takeUntil(cleanup$)
    );
  }

  /**
   * Calculate progressive interval based on no-change count
   */
  private calculateProgressiveInterval(
    baseInterval: number,
    noChangeCount: number,
    maxInterval: number
  ): number {
    // Increase interval every 10 unchanged polls
    const multiplier = Math.floor(noChangeCount / 10) + 1;
    const newInterval = baseInterval * multiplier;

    return Math.min(newInterval, maxInterval);
  }

  /**
   * Stop polling for a specific poll ID
   */
  stopPolling(pollId: string): void {

    const cleanup$ = this.activePolls.get(pollId);
    if (cleanup$) {
      cleanup$.next();
      cleanup$.complete();
      this.activePolls.delete(pollId);
    }

    // Update state if no more active polls
    if (this.activePolls.size === 0) {
      this.pollingState.update(state => ({
        ...state,
        isPolling: false
      }));
    }
  }

  /**
   * Stop all active polling
   */
  stopAllPolling(): void {

    this.activePolls.forEach((cleanup$, pollId) => {
      cleanup$.next();
      cleanup$.complete();
    });

    this.activePolls.clear();

    this.pollingState.update(state => ({
      ...state,
      isPolling: false
    }));
  }

  /**
   * Force immediate refresh for a specific URL
   */
  refreshImmediately<T>(url: string): Observable<T> {
    return this.http.get<T>(url);
  }

  /**
   * Get current app visibility state
   */
  getVisibilityState(): boolean {
    return this.isAppVisible();
  }

  /**
   * Get current polling state (reactive)
   */
  getPollingState() {
    return this.pollingState.asReadonly();
  }

  /**
   * Observable that emits when app visibility changes
   */
  getVisibilityChanges(): Observable<boolean> {
    return this.visibilityChange$.asObservable();
  }

  /**
   * Cleanup on service destroy
   */
  ngOnDestroy(): void {
    this.stopAllPolling();
  }
}
