import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {Observable} from "rxjs";
import {CheckpointRepresentation, ParticipantToPassCheckpointRepresentation, RandonneurCheckPointRepresentation} from "../shared/api/api";
import {environment} from "../../environments/environment";
import {map, shareReplay, take, tap} from "rxjs/operators";
import {TrackService} from "../competitor/track.service";
import {EventService} from "../admin/event-admin/event.service";

@Injectable({
  providedIn: 'root'
})
export class VolonteerService {

  constructor(private httpClient: HttpClient,
              private linkService: LinkService) { }


  public getCheckpoints( trackuid: string, checkpoint_uid: string): Observable<Array<ParticipantToPassCheckpointRepresentation>>{
    const path = "volonteer/track/" + trackuid + "/checkpoint/" + checkpoint_uid + "/randonneurs";
    return this.httpClient.get<Array<ParticipantToPassCheckpointRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: Array<ParticipantToPassCheckpointRepresentation>) => {
        return checkpoints;
      }),
      tap((checkpoints: Array<ParticipantToPassCheckpointRepresentation>) => {
        console.log(checkpoints);
      }),
      shareReplay(1)
    ) as Observable<Array<ParticipantToPassCheckpointRepresentation>>;
  }

  public getCheckpointsForTrack(trackuid: string){
    const path = "volonteer/track/" + trackuid + "/checkpoints";
    return this.httpClient.get<Array<CheckpointRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: Array<CheckpointRepresentation>) => {
        return checkpoints;
      }),
      tap((checkpoints: Array<CheckpointRepresentation>) => {
        console.log(checkpoints);
      }),
      shareReplay(1)
    ) as Observable<Array<CheckpointRepresentation>>;

  }
}
