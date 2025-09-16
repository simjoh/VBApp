import { Injectable, OnDestroy } from '@angular/core';
import {SiteService} from "../../../admin/shared/service/site.service";
import {map, shareReplay, catchError, takeUntil} from "rxjs/operators";
import {BehaviorSubject, Observable, of, Subject} from "rxjs";
import { Site } from '../../api/api';

@Injectable()
export class SiteSelectorComponentService implements OnDestroy {
  private destroy$ = new Subject<void>();

  $currentSiteSubject = new BehaviorSubject<Site>(null);

  $allSites = this.siteService.getAllSites().pipe(
    map((sites) => {
      return sites;
    }),
    catchError(error => {
      console.error('SiteSelectorComponentService: Error in getAllSites():', error);
      console.error('SiteSelectorComponentService: Error status:', error.status);
      console.error('SiteSelectorComponentService: Error message:', error.message);
      console.error('SiteSelectorComponentService: Full error object:', error);
      // Return empty array instead of throwing
      return of([]);
    }),
    shareReplay(1),
    takeUntil(this.destroy$)
  );

  constructor(private siteService: SiteService) {
    // Remove the force subscription that was causing memory leak
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  currentEvent(selectedSite: Site) {

  }
}
