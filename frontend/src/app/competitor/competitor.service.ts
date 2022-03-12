import { Injectable } from '@angular/core';
import {Observable} from "rxjs";
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

   public stampOnCheckpoint(){
    const link = this.linkService.findByRel([],HttpMethod.POST)
    return this.httpClient.post<EventRepresentation>(environment.backend_url + "event/", event).pipe(
      map((site: EventRepresentation) => {
        return site;
      }),
      tap(event =>   console.log("Stamped competitor on checkpoint", event))
    ).toPromise();
  }


  public rollbackStamp(){
    return this.httpClient.put<EventRepresentation>(environment.backend_url + "event", {} as EventRepresentation).pipe(
      map((event: EventRepresentation) => {
        return event;
      }),
      tap(event =>   console.log(event))
    ) as Observable<EventRepresentation>
  }

  public markAsDNF(){
    return this.httpClient.put<EventRepresentation>(environment.backend_url + "event", {} as EventRepresentation).pipe(
      map((event: EventRepresentation) => {
        return event;
      }),
      tap(event =>   console.log(event))
    ) as Observable<EventRepresentation>
  }

  public rollbackDNF(){
    return this.httpClient.put<EventRepresentation>(environment.backend_url + "event", {} as EventRepresentation).pipe(
      map((event: EventRepresentation) => {
        return event;
      }),
      tap(event =>   console.log(event))
    ) as Observable<EventRepresentation>
  }
}
