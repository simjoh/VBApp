import {Injectable, isDevMode} from '@angular/core';
import { HttpClient } from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {EventInformationRepresentation, TrackRepresentation} from "./api/api";
import {environment} from "../../environments/environment";
import {map, mergeMap, shareReplay, take, tap} from "rxjs/operators";
import {BehaviorSubject, Observable, Subject} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class EventTrackInformationService {

  private refreshSubject = new BehaviorSubject<number>(0);

  constructor(private httpClient: HttpClient, private linkService: LinkService) { }

public getEventsAndTracks(){
  const path = 'events/eventInformation';
  return this.refreshSubject.pipe(
    mergeMap(() =>
      this.httpClient.get<Array<EventInformationRepresentation>>(environment.backend_url + path).pipe(
        map((tracks: Array<EventInformationRepresentation>) => {
          return tracks;
        })
      )
    )
  ) as Observable<Array<any>>;
}

public refresh() {
  this.refreshSubject.next(Date.now());
}

}
