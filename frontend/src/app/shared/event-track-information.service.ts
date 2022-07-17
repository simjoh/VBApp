import {Injectable, isDevMode} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {EventInformationRepresentation, TrackRepresentation} from "./api/api";
import {environment} from "../../environments/environment";
import {map, mergeMap, shareReplay, take, tap} from "rxjs/operators";
import {BehaviorSubject, Observable, Subject} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class EventTrackInformationService {




  constructor(private httpClient: HttpClient, private linkService: LinkService) { }

public getEventsAndTracks(){
  const path = '/events/eventInformation';
  return this.httpClient.get<Array<EventInformationRepresentation>>(environment.backend_url + path).pipe(
    take(1),
    map((tracks: Array<EventInformationRepresentation>) => {
      return tracks;
    }),
    tap((tracks: Array<EventInformationRepresentation>) => {
      console.log(tracks);
    }),
    shareReplay(1)
  ) as Observable<Array<any>>;
}

}
