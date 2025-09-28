import { Component, OnInit, OnDestroy, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { MsrStatsService, EventStats, MsrEvent } from '../../shared/services/msr-stats.service';
import { TranslationService } from '../../core/services/translation.service';
import { timeout, catchError, takeUntil } from 'rxjs/operators';
import { of, Subject } from 'rxjs';

@Component({
  selector: 'app-msr',
  templateUrl: './msr.component.html',
  styleUrls: ['./msr.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MsrComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();

  stats: EventStats | null = null;
  loading = false;
  error: string | null = null;

  // MSR Events dropdown
  msrEvents: MsrEvent[] = [];
  selectedEventUid: string = '';
  loadingEvents = false;
  eventsError: string | null = null;

  constructor(
    private msrStatsService: MsrStatsService,
    private cdr: ChangeDetectorRef,
    private translationService: TranslationService
  ) { }

  ngOnInit(): void {
    this.loadMsrEvents();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadMsrEvents(): void {
    console.log('MsrComponent: Loading MSR events...');
    this.loadingEvents = true;
    this.eventsError = null;

    this.msrStatsService.getMsrEvents()
      .pipe(
        timeout(10000),
        takeUntil(this.destroy$),
        catchError(error => {
          console.error('MsrComponent: Error loading MSR events:', error);
          return of(null);
        })
      )
      .subscribe({
        next: (response) => {
          if (response) {
            this.msrEvents = response.data;
            console.log('MsrComponent: Loaded', this.msrEvents.length, 'MSR events');

            // Auto-select first event if available
            if (this.msrEvents.length > 0) {
              this.selectedEventUid = this.msrEvents[0].event_uid;
              console.log('MsrComponent: Auto-selected first event:', this.selectedEventUid);
            }
          }
          this.loadingEvents = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          console.error('MsrComponent: Error loading MSR events:', error);
          this.eventsError = this.translationService.translate('msr.errorLoadingEvents');
          this.loadingEvents = false;
          this.cdr.markForCheck();
        }
      });
  }

  loadStats(): void {
    if (!this.selectedEventUid) {
      this.error = this.translationService.translate('msr.selectEvent') + '.';
      return;
    }

    console.log('MsrComponent: loadStats() called for event:', this.selectedEventUid);
    this.loading = true;
    this.error = null;

    this.msrStatsService.getEventStats(this.selectedEventUid)
      .pipe(
        timeout(10000), // 10 second timeout
        takeUntil(this.destroy$),
        catchError(error => {
          console.error('MsrComponent: Timeout or error in pipe:', error);
          return of(null);
        })
      )
      .subscribe({
        next: (stats) => {
          console.log('MsrComponent: Stats received successfully:', stats);
          this.stats = stats;
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          console.error('MsrComponent: Error loading MSR stats:', error);
          this.error = this.translationService.translate('msr.errorLoadingStats');
          this.loading = false;
          this.cdr.markForCheck();
        }
      });
  }

  refreshStats(): void {
    this.loadStats();
  }

  getSelectedEventTitle(): string {
    const selectedEvent = this.msrEvents.find(event => event.event_uid === this.selectedEventUid);
    return selectedEvent ? selectedEvent.title : 'Välj evenemang';
  }

  onEventChange(): void {
    console.log('MsrComponent: Event changed to:', this.selectedEventUid);
    // Clear previous stats when event changes
    this.stats = null;
    this.error = null;
  }
}
