import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import {combineLatest, Observable, Subject, throwError} from "rxjs";
import {catchError, map, shareReplay, startWith, tap} from "rxjs/operators";
import { Site } from 'src/app/shared/api/api';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class SiteService {

  removeSubject = new Subject<string>()
  relaod$ = this.removeSubject.asObservable().pipe(
    startWith(''),
  );

  // allSites$ = this.getAllSites() as Observable<Site[]>;

  private siteInsertedSubject = new Subject<Site>();
  siteInsertedAction$ = this.siteInsertedSubject.asObservable().pipe(
    startWith(''),
  );

  constructor(private httpClient: HttpClient) { }


  async newSite(newSite: Site) {
    const user = await this.addSite(newSite)
    this.siteInsertedSubject.next(user);
  }

   getAllSites(): Observable<Site[]>{
    console.log('SiteService: getAllSites() called, requesting:', environment.backend_url + "sites");

    // Log localStorage content for debugging
    const loggedInUser = localStorage.getItem('loggedInUser');
    const apiKey = environment.api_key;
    console.log('SiteService: localStorage loggedInUser exists:', !!loggedInUser);
    console.log('SiteService: localStorage loggedInUser content:', loggedInUser);
    console.log('SiteService: environment api_key:', apiKey);

    return this.httpClient.get<Site[]>(environment.backend_url + "sites").pipe(
      tap(() => console.log('SiteService: HTTP request initiated')),
      map((sites: Array<Site>) => {
        console.log('SiteService: getAllSites() received response, sites count:', sites ? sites.length : 'null/undefined');
        console.log('SiteService: Response type:', typeof sites);
        console.log('SiteService: Is array:', Array.isArray(sites));
        if (sites && Array.isArray(sites) && sites.length > 0) {
          console.log('SiteService: First site example:', sites[0]);
        }
        return sites;
      }),
      tap(sites =>   console.log("SiteService: All sites after map" ,sites)),
      catchError(error => {
        console.error('SiteService: getAllSites() error:', error);
        console.error('SiteService: Error status:', error.status);
        console.error('SiteService: Error statusText:', error.statusText);
        console.error('SiteService: Error message:', error.message);
        console.error('SiteService: Error url:', error.url);
        if (error.error) {
          console.error('SiteService: Error body:', error.error);
        }
        console.error('SiteService: Full error object:', error);
        return throwError(error);
      }),
      shareReplay(1)
    );
  }

  siteWithAdd$ = combineLatest([this.getAllSites(), this.siteInsertedAction$, this.relaod$]).pipe(
    map(([all, insert, del]) =>  {
      console.log('SiteService: $all combineLatest data:', {
        allSites: all ? all.length : 'null/undefined',
        insert: insert,
        del: del
      });

      if(insert){
        return  [...all, insert]
      }
      if(del){
        var index = all.findIndex((elt) => elt.site_uid === del);
        all.splice(index, 1);
        const userArray = all;
        return   this.deepCopyProperties(all);
      }
      return this.deepCopyProperties(all);
    }),
  );

  public getSite(siteUid: string): Observable<Site> {
    return this.httpClient.get<Site>(environment.backend_url + "site/" + siteUid).pipe(
      map((site: Site) => {
        return site;
      }),
      tap(site =>   console.log(site))
    ) as Observable<Site>
  }

  async addSite(site: Site){
    return await this.httpClient.post<Site>(environment.backend_url + "site", site).pipe(
      map((site: Site) => {
        return site;
      }),
      tap(site =>   console.log(site))
    ).toPromise();
  }

  public deleteSite(siteUid: string){
    return this.httpClient.delete(environment.backend_url + "site/" + siteUid)
      .pipe(
        catchError(err => {
          return throwError(err);
        })
      ).toPromise().then((s) => {
        this.removeSubject.next(siteUid);
      })
  }

  public updateUser(useruid: string, user: Site){
    return this.httpClient.put<Site>(environment.backend_url + "site", {} as Site).pipe(
      map((site: Site) => {
        return site;
      }),
      tap(site =>   console.log(site))
    ) as Observable<Site>
  }

  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }
}
