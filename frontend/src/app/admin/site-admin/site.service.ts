import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable, Subject, throwError} from "rxjs";
import {Site} from "../../shared/api/api";
import {environment} from "../../../environments/environment";
import {catchError, map, shareReplay, startWith, tap} from "rxjs/operators";

@Injectable({
  providedIn: 'root'
})
export class SiteService {

  removeSubject = new Subject<string>()
  relaod$ = this.removeSubject.asObservable().pipe(
    startWith(''),
  );

  allSites$ = this.getAllSites() as Observable<Site[]>;

  private userInsertedSubject = new Subject<Site>();
  userInsertedAction$ = this.userInsertedSubject.asObservable().pipe(
    startWith(''),
  );

  constructor(private httpClient: HttpClient) { }


  async newSite(newSite: Site) {
    const user = await this.addSite(newSite)
    this.userInsertedSubject.next(user);
  }

  private getAllSites(): Observable<Site[]>{
    return this.httpClient.get<Site[]>(environment.backend_url + "sites").pipe(
      map((sites: Array<Site>) => {
        return sites;
      }),
      tap(sites =>   console.log("All sites" ,sites)),
      shareReplay(1)
    );
  }

  public getSite(siteUid: string): Observable<Site> {
    return this.httpClient.get<Site>(environment.backend_url + "site/" + siteUid).pipe(
      map((site: Site) => {
        return site;
      }),
      tap(site =>   console.log(site))
    ) as Observable<Site>
  }

  async addSite(site: Site){
    return await this.httpClient.post<Site>(environment.backend_url + "site/", site).pipe(
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
}
