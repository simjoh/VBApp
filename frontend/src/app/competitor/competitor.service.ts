import { Injectable } from '@angular/core';
import {Observable, Subject} from "rxjs";
import {EventRepresentation, RandonneurCheckPointRepresentation} from "../shared/api/api";
import {environment} from "../../environments/environment";
import {map, mergeMap, shareReplay, take, tap} from "rxjs/operators";
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {HttpMethod} from "../core/HttpMethod";

@Injectable({
  providedIn: 'root'
})
export class CompetitorService {



  constructor(private httpClient: HttpClient, private linkService: LinkService) { }

  public getCheckpoints(startnumber: number, trackuid: string, id: string): Observable<Array<RandonneurCheckPointRepresentation>>{
    const path = "randonneur/" + id + "/track/" + trackuid + "/startnumber/" + startnumber;
    return this.httpClient.get<Array<RandonneurCheckPointRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: Array<RandonneurCheckPointRepresentation>) => {
        return checkpoints;
      }),
      tap((checkpoints: Array<RandonneurCheckPointRepresentation>) => {
        console.log(checkpoints);
      }),
      shareReplay(1)
    ) as Observable<Array<RandonneurCheckPointRepresentation>>;
  }

   public stampOnCheckpoint(s: RandonneurCheckPointRepresentation){
    const link = this.linkService.findByRel(s.links,'relation.randonneur.stamp', HttpMethod.POST)
    return this.httpClient.post<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>   console.log("Stamped competitor on checkpoint", event))
    ).toPromise();
  }


  public rollbackStamp(s: RandonneurCheckPointRepresentation){
    const link = this.linkService.findByRel(s.links,'relation.randonneur.rollback', HttpMethod.PUT)
    return this.httpClient.put<any>( link.url, null).pipe(
      map((event: boolean) => {
        return event;
      }),
      tap(event =>   console.log(event))
    ).toPromise()
  }

  public markAsDNF(s: RandonneurCheckPointRepresentation){
    const link = this.linkService.findByRel(s.links,'relation.randonneur.dnf', HttpMethod.PUT)

    return this.httpClient.put<any>(link.url, null).pipe(
      map((event: boolean) => {
        return event;
      }),
      tap(event =>   console.log(event))
    ).toPromise()
  }

  public rollbackDNF(s: RandonneurCheckPointRepresentation){
    const link = this.linkService.findByRel(s.links,'relation.randonneur.dnf.rollback', HttpMethod.PUT)
    return this.httpClient.put<any>(link.url,null).pipe(
      map((event: boolean) => {
        return event;
      }),
      tap(event =>   console.log(event))
    ).toPromise();
  }
}
