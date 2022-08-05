import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../../core/link.service";
import {ClubRepresentation, TrackRepresentation} from "../../shared/api/api";
import {environment} from "../../../environments/environment";
import {map, shareReplay, take, tap} from "rxjs/operators";
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class ClubService {


  $allclubs = this.getAllClubs();

  constructor(private httpClient: HttpClient, private linkService: LinkService) { }



  private getAllClubs(): Observable<Array<ClubRepresentation>>{
    const path = '/club/allclubs';
    return this.httpClient.get<Array<ClubRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((tracks: Array<ClubRepresentation>) => {
        return tracks.sort((a, b) => (a.title > b.title) ? 1 : -1)
      }),
      tap((tracks: Array<ClubRepresentation>) => {
        console.log(tracks);
      }),
      shareReplay(1)
    ) as Observable<Array<ClubRepresentation>>;
  }
}
