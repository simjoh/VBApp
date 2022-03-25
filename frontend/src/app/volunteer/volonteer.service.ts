import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {Observable} from "rxjs";
import {CheckpointRepresentation, ParticipantToPassCheckpointRepresentation, RandonneurCheckPointRepresentation} from "../shared/api/api";
import {environment} from "../../environments/environment";
import {map, shareReplay, take, tap} from "rxjs/operators";
import {TrackService} from "../competitor/track.service";
import {EventService} from "../admin/event-admin/event.service";
import {HttpMethod} from "../core/HttpMethod";

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


  public checkinParticipant(product: any): Promise<boolean>{
    const link = this.linkService.findByRel(product.link,'relation.volonteer.stamp', HttpMethod.PUT)
    return this.httpClient.post<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>   console.log("Stamped competitor on checkpoint", event))
    ).toPromise();

  }

  public rollbackParticipantCheckin(product: any){
    const link = this.linkService.findByRel(product.link,'relation.volonteer.rollbackstamp', HttpMethod.PUT)
    return this.httpClient.put<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>   console.log("rollback checkin on checkpoint", event))
    ).toPromise();
  }

  rollbackDnf(product: any) {
    const link = this.linkService.findByRel(product.link,'relation.volonteer.rollbackdnf', HttpMethod.PUT)
    return this.httpClient.put<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>   console.log("Stamped competitor on checkpoint", event))
    ).toPromise();
  }

  setDnf(product: any) {
    const link = this.linkService.findByRel(product.link,'relation.volonteer.setdnf', HttpMethod.PUT)
    return this.httpClient.post<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>   console.log("Stamped competitor on checkpoint", event))
    ).toPromise();
  }
}
