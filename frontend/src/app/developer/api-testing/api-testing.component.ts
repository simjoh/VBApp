import { Component, OnInit, OnDestroy, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { LanguageService } from '../../core/services/language.service';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'brevet-api-testing',
  templateUrl: './api-testing.component.html',
  styleUrls: ['./api-testing.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ApiTestingComponent implements OnInit, OnDestroy {

  response: any = null;
  loading = false;
  error: string | null = null;
  errorEvents: any[] = [];
  selectedErrorEvents: string[] = [];

  // Performance optimization: Use Set for O(1) lookups
  private selectedErrorEventsSet = new Set<string>();
  private destroy$ = new Subject<void>();

  constructor(
    private http: HttpClient,
    private cdr: ChangeDetectorRef,
    public languageService: LanguageService
  ) { }

  ngOnInit(): void {
    // Removed console.log for performance
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  private getJwtToken(): string | null {
    // Try to get token from localStorage or sessionStorage
    return localStorage.getItem('token') || sessionStorage.getItem('token') || null;
  }

  runMainApiMigration(): void {
    this.loading = true;
    this.error = null;
    this.cdr.markForCheck();

    const token = this.getJwtToken();

    const headers = new HttpHeaders({
      'APIKEY': 'notsecret_developer_key',
      'Content-Type': 'application/json',
      ...(token && { 'TOKEN': token })
    });

    this.http.get('/api/infra/migrations/migrate', { headers })
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response) => {
          this.response = {
            status: 'Success',
            data: response,
            timestamp: new Date().toISOString(),
            service: 'Main API'
          };
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          this.error = error.message || 'Main API migration failed';
          this.response = {
            status: 'Error',
            error: error,
            timestamp: new Date().toISOString(),
            service: 'Main API'
          };
          this.loading = false;
          this.cdr.markForCheck();
        }
      });
  }

  runLoppserviceMigration(): void {
    this.loading = true;
    this.error = null;
    this.cdr.markForCheck();

    const apiUrl = environment.loppservice_url || '/loppservice/';
    const apiKey = 'testkey';
    const token = this.getJwtToken();

    const headers = new HttpHeaders({
      'apikey': apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token && { 'TOKEN': token })
    });

    this.http.get(`${apiUrl}api/artisan/migrate`, { headers })
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response) => {
          this.response = {
            status: 'Success',
            data: response,
            timestamp: new Date().toISOString(),
            service: 'Loppservice'
          };
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          this.error = error.message || 'Loppservice migration failed';
          this.response = {
            status: 'Error',
            error: error,
            timestamp: new Date().toISOString(),
            service: 'Loppservice'
          };
          this.loading = false;
          this.cdr.markForCheck();
        }
      });
  }

  runCacheCommands(): void {
    this.loading = true;
    this.error = null;
    this.cdr.markForCheck();

    const apiUrl = environment.loppservice_url || '/loppservice/';
    const apiKey = 'testkey';
    const token = this.getJwtToken();

    const headers = new HttpHeaders({
      'apikey': apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token && { 'TOKEN': token })
    });

    this.http.get(`${apiUrl}api/artisan/command/cache/run`, { headers })
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response) => {
          this.response = {
            status: 'Success',
            data: response,
            timestamp: new Date().toISOString(),
            service: 'Loppservice'
          };
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          this.error = error.message || 'Cache commands failed';
          this.response = {
            status: 'Error',
            error: error,
            timestamp: new Date().toISOString(),
            service: 'Loppservice'
          };
          this.loading = false;
          this.cdr.markForCheck();
        }
      });
  }

  loadErrorEvents(): void {
    this.loading = true;
    this.error = null;
    this.cdr.markForCheck();

    const apiUrl = environment.loppservice_url || '/loppservice/';
    const apiKey = 'testkey';
    const token = this.getJwtToken();

    const headers = new HttpHeaders({
      'apikey': apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token && { 'TOKEN': token })
    });

    this.http.get<any>(`${apiUrl}api/integration/error-events?limit=20`, {
      headers,
      withCredentials: false
    })
    .pipe(takeUntil(this.destroy$))
    .subscribe({
      next: (response: any) => {
        if (response.success) {
          this.errorEvents = response.data || [];
          this.selectedErrorEvents = []; // Clear selections when loading new data
          this.selectedErrorEventsSet.clear(); // Clear Set as well
          this.response = {
            status: 'Success',
            data: `Loaded ${this.errorEvents.length} error events`,
            timestamp: new Date().toISOString(),
            service: 'Loppservice'
          };
        } else {
          this.error = response.message || 'Failed to load error events';
          this.response = {
            status: 'Error',
            error: response,
            timestamp: new Date().toISOString(),
            service: 'Loppservice'
          };
        }
        this.loading = false;
        this.cdr.markForCheck();
      },
      error: (error) => {
        this.error = error.message || 'Failed to load error events';
        this.response = {
          status: 'Error',
          error: error,
          timestamp: new Date().toISOString(),
          service: 'Loppservice'
        };
        this.loading = false;
        this.cdr.markForCheck();
      }
    });
  }


  // Error Events Selection methods - Optimized for performance
  isErrorEventSelected(eventUid: string): boolean {
    return this.selectedErrorEventsSet.has(eventUid);
  }

  toggleErrorEventSelection(eventUid: string): void {
    if (this.selectedErrorEventsSet.has(eventUid)) {
      this.selectedErrorEventsSet.delete(eventUid);
    } else {
      this.selectedErrorEventsSet.add(eventUid);
    }
    // Update array for template binding
    this.selectedErrorEvents = Array.from(this.selectedErrorEventsSet);
    this.cdr.markForCheck();
  }

  isAllErrorEventsSelected(): boolean {
    return this.errorEvents.length > 0 && this.selectedErrorEventsSet.size === this.errorEvents.length;
  }

  isSomeErrorEventsSelected(): boolean {
    return this.selectedErrorEventsSet.size > 0 && this.selectedErrorEventsSet.size < this.errorEvents.length;
  }

  toggleAllErrorEventsSelection(): void {
    if (this.isAllErrorEventsSelected()) {
      this.selectedErrorEventsSet.clear();
    } else {
      this.errorEvents.forEach(event => {
        this.selectedErrorEventsSet.add(event.errorevent_uid);
      });
    }
    // Update array for template binding
    this.selectedErrorEvents = Array.from(this.selectedErrorEventsSet);
    this.cdr.markForCheck();
  }

  retrySelectedErrorEvents(): void {
    if (this.selectedErrorEvents.length === 0) {
      return;
    }

    this.loading = true;
    this.error = null;
    this.cdr.markForCheck();

    const apiUrl = environment.loppservice_url || '/loppservice/';
    const apiKey = 'testkey';
    const token = this.getJwtToken();

    const headers = new HttpHeaders({
      'apikey': apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token && { 'TOKEN': token })
    });

    // Filter only eventregistration type events for retry
    const eventregistrationEvents = this.selectedErrorEvents.filter(eventUid => {
      const event = this.errorEvents.find(e => e.errorevent_uid === eventUid);
      return event && event.type === 'eventregistration';
    });

    if (eventregistrationEvents.length === 0) {
      this.response = {
        status: 'Warning',
        data: {
          message: 'No eventregistration type events selected for retry',
          selected_count: this.selectedErrorEvents.length,
          retryable_count: 0
        },
        timestamp: new Date().toISOString(),
        service: 'Loppservice'
      };
      this.loading = false;
      this.cdr.markForCheck();
      return;
    }

    // Retry each selected event individually
    let completed = 0;
    let successful = 0;
    let failed = 0;
    const errors: string[] = [];

    const processNextEvent = () => {
      if (completed >= eventregistrationEvents.length) {
        // All events processed
        this.response = {
          status: failed === 0 ? 'Success' : 'Partial Success',
          data: {
            message: `Retry completed. Successfully retried ${successful} events, ${failed} failed`,
            retried_count: successful,
            failed_count: failed,
            errors: errors
          },
          timestamp: new Date().toISOString(),
          service: 'Loppservice'
        };

        if (successful > 0) {
          // Reload the error events to update the list
          this.loadErrorEvents();
        }

        this.loading = false;
        this.cdr.markForCheck();
        return;
      }

      const eventUid = eventregistrationEvents[completed];

      this.http.post<any>(`${apiUrl}api/integration/retry-publish-event/${eventUid}`, {}, {
        headers,
        withCredentials: false
      })
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response: any) => {
          if (response.success) {
            successful++;
            // Remove from selected events
            this.selectedErrorEventsSet.delete(eventUid);
            this.selectedErrorEvents = Array.from(this.selectedErrorEventsSet);
            this.cdr.markForCheck();
          } else {
            failed++;
            errors.push(`Event ${eventUid}: ${response.message || 'Unknown error'}`);
          }
          completed++;
          processNextEvent();
        },
        error: (error) => {
          failed++;
          errors.push(`Event ${eventUid}: ${error.message || 'Network error'}`);
          completed++;
          processNextEvent();
        }
      });
    };

    processNextEvent();
  }

}
