import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {Observable} from "rxjs";
import {RandonneurCheckPointRepresentation} from "../shared/api/api";
import {environment} from "../../environments/environment";
import {map, shareReplay, take, tap} from "rxjs/operators";

@Injectable({
  providedIn: 'root'
})
export class TrackService {

  constructor(private httpClient: HttpClient, private linkService: LinkService) { }

  public getTrack(trackuid: string): Observable<any> {
    const path = "randonneur/track/" + trackuid;
    return this.httpClient.get<any>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: any) => {
        return checkpoints;
      }),
      shareReplay(1)
    ) as Observable<any>;
  }
}
