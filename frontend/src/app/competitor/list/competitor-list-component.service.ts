import { Injectable } from '@angular/core';
import {CompetitorService} from "../competitor.service";
import {AuthService} from "../../core/auth/auth.service";
import {map, mergeMap, shareReplay, take, tap, withLatestFrom} from "rxjs/operators";
import {RandonneurCheckPointRepresentation} from "../../shared/api/api";
import {BehaviorSubject, combineLatest, forkJoin, ReplaySubject} from "rxjs";
import {TrackService} from "../track.service";
import {LinkService} from "../../core/link.service";
import {HttpMethod} from "../../core/HttpMethod";

@Injectable()
export class CompetitorListComponentService {


  reloadSubject = new BehaviorSubject(false);
  reload$ = this.reloadSubject.asObservable();



  $controls = combineLatest([this.authService.$auth$, this.reload$]).pipe(
    mergeMap(([auth, sidor]) => {
      return this.competitorService.getCheckpoints(auth.startnumber, auth.trackuid, auth.id)
    }),
    shareReplay(1)
  );



  // $controls =  this.authService.$auth$.pipe(
  //   mergeMap((auth) => {
  //     return this.competitorService.getCheckpoints(auth.startnumber, auth.trackuid, auth.id)
  //   }),
  //   map((test: Array<RandonneurCheckPointRepresentation>) => {
  //     return test;
  //   })
  // );


  constructor(private competitorService: CompetitorService, private authService: AuthService, private trackService: TrackService,private linkservice: LinkService) {
  }

  public reload(){
    this.reloadSubject.next(true);
  }

  async  stamp($event: boolean, s: RandonneurCheckPointRepresentation):Promise<any>{
   await this.competitorService.stampOnCheckpoint(s);
    this.reload()
  }

  async rollbackStamp($event: any, s: RandonneurCheckPointRepresentation): Promise<any>{
    await this.competitorService.rollbackStamp(s);
    this.reload();
  }


  async setDnf($event: any, s: RandonneurCheckPointRepresentation): Promise<any>{
    if ($event === true){
      await this.competitorService.markAsDNF(s);
      this.reload();
    } else {
      await this.competitorService.rollbackDNF(s);
      this.reload();
    }

  }

  async dnfLinkExists(s: RandonneurCheckPointRepresentation): Promise<any>{
    return this.linkservice.findByRel(s.links,'relation.randonneur.dnf', HttpMethod.PUT)
  }
}
