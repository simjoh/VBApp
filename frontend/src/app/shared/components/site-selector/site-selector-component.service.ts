import { Injectable } from '@angular/core';
import {SiteService} from "../../../admin/shared/service/site.service";
import {map} from "rxjs/operators";
import {BehaviorSubject, Observable} from "rxjs";
import { Site } from '../../api/api';

@Injectable()
export class SiteSelectorComponentService {

  $currentSiteSubject = new BehaviorSubject<Site>(null);



  $allSites = this.siteService.getAllSites().pipe(
    map((sites) => {

      return sites;
    })
  )

  constructor(private siteService: SiteService) { }

  currentEvent(selectedSite: Site) {

  }
}
