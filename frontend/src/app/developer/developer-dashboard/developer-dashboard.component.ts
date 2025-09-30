import { Component, OnInit, OnDestroy, ChangeDetectionStrategy } from '@angular/core';
import { DeveloperStatsService, DeveloperStats } from '../services/developer-stats.service';
import { Subject, takeUntil, interval } from 'rxjs';

@Component({
  selector: 'brevet-developer-dashboard',
  templateUrl: './developer-dashboard.component.html',
  styleUrls: ['./developer-dashboard.component.scss']
})
export class DeveloperDashboardComponent implements OnInit, OnDestroy {

  stats: DeveloperStats | null = null;
  loading = false;
  error: string | null = null;

  private destroy$ = new Subject<void>();
  private refreshInterval$ = interval(60000); // Refresh every 60 seconds (1 minute)

  constructor(private statsService: DeveloperStatsService) { }

  ngOnInit(): void {
    this.loadStats();

    // Auto-refresh stats every 60 seconds
    this.refreshInterval$
      .pipe(takeUntil(this.destroy$))
      .subscribe(() => {
        this.loadStats();
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadStats(): void {
    this.loading = true;
    this.error = null;

    this.statsService.getStats()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (stats) => {
          this.stats = stats;
          this.loading = false;
        },
        error: (error) => {
          this.error = 'Failed to load statistics';
          this.loading = false;
          console.error('Error loading stats:', error);
        }
      });
  }

  getTrafficLightClass(): string {
    if (!this.stats) return 'traffic-light gray';
    return `traffic-light ${this.stats.trafficLightStatus}`;
  }

  getTrafficLightIcon(): string {
    if (!this.stats) return 'pi-circle';
    switch (this.stats.trafficLightStatus) {
      case 'green': return 'pi-check-circle';
      case 'yellow': return 'pi-exclamation-triangle';
      case 'red': return 'pi-times-circle';
      default: return 'pi-circle';
    }
  }


}
