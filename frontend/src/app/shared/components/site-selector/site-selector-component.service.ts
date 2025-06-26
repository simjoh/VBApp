import { Injectable } from '@angular/core';
import {SiteService} from "../../../admin/shared/service/site.service";
import {map, shareReplay, catchError, tap} from "rxjs/operators";
import {BehaviorSubject, Observable, of} from "rxjs";
import { Site } from '../../api/api';

@Injectable()
export class SiteSelectorComponentService {

  $currentSiteSubject = new BehaviorSubject<Site>(null);

  $allSites = this.siteService.getAllSites().pipe(
    tap(() => console.log('SiteSelectorComponentService: API call initiated')),
    map((sites) => {
      console.log('SiteSelectorComponentService: Site selector service received sites:', sites);
      console.log('SiteSelectorComponentService: Sites array length:', sites ? sites.length : 'null/undefined');
      console.log('SiteSelectorComponentService: First few sites:', sites ? sites.slice(0, 3) : 'none');
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
    console.log('SiteSelectorComponentService constructor called');

    // Force the observable to be subscribed to trigger the HTTP request
    this.$allSites.subscribe(
      sites => {
        console.log('SiteSelectorComponentService: Force subscription - sites received:', sites);
        console.log('SiteSelectorComponentService: Force subscription - sites count:', sites ? sites.length : 'null/undefined');
      },
      error => {
        console.error('SiteSelectorComponentService: Force subscription - error:', error);
      }
    );
  }

  currentEvent(selectedSite: Site) {

  }
}
