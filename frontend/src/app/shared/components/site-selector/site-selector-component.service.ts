import { Injectable } from '@angular/core';
import {SiteService} from "../../../admin/shared/service/site.service";
import {map, shareReplay, catchError} from "rxjs/operators";
import {BehaviorSubject, Observable, of} from "rxjs";
import { Site } from '../../api/api';

@Injectable()
export class SiteSelectorComponentService {

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
    shareReplay(1)
  );

  constructor(private siteService: SiteService) {
    // Force the observable to be subscribed to trigger the HTTP request
    this.$allSites.subscribe(
      sites => {
        // Subscription for triggering HTTP request
      },
      error => {
        console.error('SiteSelectorComponentService: Force subscription - error:', error);
      }
    );
  }

  currentEvent(selectedSite: Site) {

  }
}
