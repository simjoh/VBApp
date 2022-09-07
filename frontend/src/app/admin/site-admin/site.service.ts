import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {combineLatest, firstValueFrom, Observable, Subject, throwError} from "rxjs";
import {Site} from "../../shared/api/api";
import {environment} from "../../../environments/environment";
import {catchError, map, shareReplay, startWith, tap} from "rxjs/operators";
import {LinkService} from "../../core/link.service";
import {HttpMethod} from "../../core/HttpMethod";

@Injectable({
  providedIn: 'root'
})
export class SiteService {

  removeSubject = new Subject<string>()
  relaod$ = this.removeSubject.asObservable().pipe(
    startWith(''),
  );

  siteReloadAction = new Subject<Site>()
  $sitesReload = this.siteReloadAction.asObservable().pipe(
    startWith(null),
  );

  // allSites$ = this.getAllSites() as Observable<Site[]>;

  private siteInsertedSubject = new Subject<Site>();
  siteInsertedAction$ = this.siteInsertedSubject.asObservable().pipe(
    startWith(''),
  );



  constructor(private httpClient: HttpClient, private linkService: LinkService) { }


  async newSite(newSite: Site) {
    const user = await this.addSite(newSite)
    this.siteInsertedSubject.next(user);
  }

   getAllSites(): Observable<Site[]>{
    return this.httpClient.get<Site[]>(environment.backend_url + "sites").pipe(
      map((sites: Array<Site>) => {
        return sites;
      }),
      tap(sites =>   console.log("All sites" ,sites)),
      shareReplay(1)
    );
  }

  siteWithAdd$ = combineLatest([this.getAllSites(), this.siteInsertedAction$, this.relaod$, this.$sitesReload]).pipe(
    map(([all, insert, del, reload]) =>  {
      if(insert){
        return  [...all, insert]
      }
      if(del){
        var index = all.findIndex((elt) => elt.site_uid === del);
        all.splice(index, 1);
        const userArray = all;
        return   this.deepCopyProperties(all);
      }

      if (reload){
        var indexreload = all.findIndex((elt) => elt.site_uid === reload.site_uid);
        all[indexreload] = reload;

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

  public updateUser(site: Site){
    const  link = this.linkService.findByRel(site.links, 'relation.site.update', HttpMethod.PUT)
    return this.httpClient.put<Site>(link.url, site).pipe(
      map((site: Site) => {
        this.siteReloadAction.next(site);
        return site;
      }),
      tap(site =>   console.log(site))
    ).toPromise();
  }

  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }
}
