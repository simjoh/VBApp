import { Injectable } from '@angular/core';
import {Observable, Subject} from "rxjs";
import {EventRepresentation, RandonneurCheckPointRepresentation} from "../shared/api/api";
import {environment} from "../../environments/environment";
import {map, mergeMap, shareReplay, take, tap} from "rxjs/operators";
import { HttpClient } from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {HttpMethod} from "../core/HttpMethod";
import {MessageService} from "primeng/api";

@Injectable({
  providedIn: 'root'
})
export class CompetitorService {



  constructor(private httpClient: HttpClient, private linkService: LinkService, private messageService: MessageService) { }

  public getCheckpoints(startnumber: number, trackuid: string, id: string): Observable<Array<RandonneurCheckPointRepresentation>>{
    const path = "randonneur/" + id + "/track/" + trackuid + "/startnumber/" + startnumber;
    return this.httpClient.get<Array<RandonneurCheckPointRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: Array<RandonneurCheckPointRepresentation>) => {
        return checkpoints;
      }),
      shareReplay(1)
    ) as Observable<Array<RandonneurCheckPointRepresentation>>;
  }
  public getCheckpointsPreview(trackuid: string): Observable<Array<RandonneurCheckPointRepresentation>>{
    const path = "randonneur/preview/checkpoints/track/" + trackuid;
    return this.httpClient.get<Array<RandonneurCheckPointRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((checkpoints: Array<RandonneurCheckPointRepresentation>) => {
        return checkpoints;
      }),
      shareReplay(1)
    ) as Observable<Array<RandonneurCheckPointRepresentation>>;
  }

  async stampOnCheckpoint(s: RandonneurCheckPointRepresentation, lat: any, long: any): Promise<any>{
    const link = this.linkService.findByRel(s.links,'relation.randonneur.stamp', HttpMethod.POST)
    return await this.httpClient.post<any>(link.url + "?lat=" + lat + "&long=" + long, null).pipe(
      map((site: boolean) => {
        return site;
      }),
      tap(event =>  {
       // this.addMessage('Checkin ok on checkpoint ' + s.checkpoint.site.adress,'success','Success');
      })
    ).toPromise();
  }


  async rollbackStamp(s: RandonneurCheckPointRepresentation):Promise<any>{
    const link = this.linkService.findByRel(s.links,'relation.randonneur.rollback', HttpMethod.PUT)
    return await this.httpClient.put<any>( link.url, null).pipe(
      map((event: boolean) => {
        return event;
      }),
      tap(event =>   {
      //  this.addMessage('Rollback checkin succeed','success','Success');
      })
    ).toPromise()
  }


    async checkout(s: RandonneurCheckPointRepresentation):Promise<any>{
        const link = this.linkService.findByRel(s.links,'relation.randonneur.checkout', HttpMethod.PUT)
        return await this.httpClient.put<any>( link.url, null).pipe(
            map((event: boolean) => {
                return event;
            }),
            tap(event =>   {
                //  this.addMessage('Rollback checkin succeed','success','Success');
            })
        ).toPromise()
    }

    async undocheckout(s: RandonneurCheckPointRepresentation):Promise<any>{
        const link = this.linkService.findByRel(s.links,'relation.randonneur.undocheckout', HttpMethod.PUT)
        return await this.httpClient.put<any>( link.url, null).pipe(
            map((event: boolean) => {
                return event;
            }),
            tap(event =>   {
                //  this.addMessage('Rollback checkin succeed','success','Success');
            })
        ).toPromise()
    }

  async markAsDNF(s: RandonneurCheckPointRepresentation): Promise<any>{
    const link = this.linkService.findByRel(s.links,'relation.randonneur.dnf', HttpMethod.PUT)

    return await this.httpClient.put<any>(link.url, null).pipe(
      map((event: boolean) => {
        return event;
      }),
      tap(event =>   {
       // this.addMessage('Success set DNF','success','Success');
      })
    ).toPromise()
  }

 async  rollbackDNF(s: RandonneurCheckPointRepresentation): Promise<any>{
    const link = this.linkService.findByRel(s.links,'relation.randonneur.dnf.rollback', HttpMethod.PUT)
    return await this.httpClient.put<any>(link.url,null).pipe(
      map((event: boolean) => {
        return event;
      }),
      tap(event =>  {
      //  this.addMessage('Success rollback DNF','success','Success');
      })
    ).toPromise();
  }

  private addMessage(message: string, servity: string, summary: string){
    this.messageService.add({key: 'tc', severity:servity, summary: summary, detail: message});
  }
}
