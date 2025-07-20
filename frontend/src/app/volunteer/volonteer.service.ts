import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {Observable} from "rxjs";
import {CheckpointRepresentation, ParticipantToPassCheckpointRepresentation, RandonneurCheckPointRepresentation} from "../shared/api/api";
import {environment} from "../../environments/environment";
import {map, shareReplay, take, tap} from "rxjs/operators";
import {HttpMethod} from "../core/HttpMethod";
import {MessageService} from "primeng/api";

@Injectable({
  providedIn: 'root'
})
export class VolonteerService {

  constructor(private httpClient: HttpClient,
              private linkService: LinkService, private messageService: MessageService) { }


  public getCheckpoints( trackuid: string, checkpoint_uid: string): Observable<Array<ParticipantToPassCheckpointRepresentation>>{
    const path = "volonteer/track/" + trackuid + "/checkpoint/" + checkpoint_uid + "/randonneurs";
    return this.httpClient.get<Array<ParticipantToPassCheckpointRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: Array<ParticipantToPassCheckpointRepresentation>) => {
        return checkpoints;
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
      shareReplay(1)
    ) as Observable<Array<CheckpointRepresentation>>;
  }


  public checkinParticipant(product: any): Promise<boolean>{
    const link = this.linkService.findByRel(product.link,'relation.volonteer.stamp', HttpMethod.PUT)
    return this.httpClient.post<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>  {
        this.addMessage('Checkin ok på checkpoint ' + product.adress,'success','Success')
      })
    ).toPromise();

  }

  public rollbackParticipantCheckin(product: any){
    const link = this.linkService.findByRel(product.link,'relation.volonteer.rollbackstamp', HttpMethod.PUT)
    return this.httpClient.put<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>   {
        this.addMessage('Ångrat checkin','success','Success');
      })
    ).toPromise();
  }

  rollbackDnf(product: any) {
    const link = this.linkService.findByRel(product.link,'relation.volonteer.rollbackdnf', HttpMethod.PUT)
    return this.httpClient.put<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>   {
        this.addMessage('Ångrat deltgare DNF','success','Success');
      })
    ).toPromise();
  }

  setDnf(product: any) {
    const link = this.linkService.findByRel(product.link,'relation.volonteer.setdnf', HttpMethod.PUT)
    return this.httpClient.post<any>(link.url, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>  {
        this.addMessage('Deltagare DNF','success','Success');
      })
    ).toPromise();
  }


  private addMessage(message: string, servity: string, summary: string){
    this.messageService.add({key: 'tc', severity:servity, summary: summary, detail: message});
  }



  public undocheckout(product: any){
    const link = this.linkService.findByRel(product.link,'relation.volonteer.undocheckout', HttpMethod.PUT)
    return this.httpClient.put<any>(link.url, null).pipe(
        map((site: boolean) => {
          return site;
        }),
        tap(event =>   {
          this.addMessage('Ångrat checkout','success','Success');
        })
    ).toPromise();
  }



  public checkout(product: any): Promise<boolean>{
    const link = this.linkService.findByRel(product.link,'relation.volonteer.checkout', HttpMethod.PUT)
    return this.httpClient.put<any>(link.url, null).pipe(
        map((site: boolean) => {
          return site;
        }),
        tap(event =>  {
          this.addMessage('Checkout ok på checkpoint ' + product.adress,'success','Success')
        })
    ).toPromise();

  }
}
