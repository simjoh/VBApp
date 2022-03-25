import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {RandonneurCheckPointRepresentation} from "./api/api";
import {environment} from "../../environments/environment";
import {map, shareReplay, take, tap} from "rxjs/operators";
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class TrackService {

  constructor(private httpClient: HttpClient, private linkService: LinkService) { }


  public tracksForEvent(eventuid: string){
    const path = 'tracks/event/' + eventuid;
    return this.httpClient.get<Array<any>>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: Array<any>) => {
        return checkpoints;
      }),
      tap((checkpoints: Array<any>) => {
        console.log(checkpoints);
      }),
      shareReplay(1)
    ) as Observable<Array<any>>;
  }
}
