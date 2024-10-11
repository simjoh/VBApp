import {Injectable} from '@angular/core';
import { HttpClient } from "@angular/common/http";
import {LinkService} from "../core/link.service";
import {environment} from "../../environments/environment";
import {catchError, map, mergeMap, shareReplay, take, tap} from "rxjs/operators";
import {BehaviorSubject, Observable, throwError} from "rxjs";
import {ParticipantInformationRepresentation, ParticipantRepresentation, RandonneurCheckPointRepresentation} from "./api/api";
import {HttpMethod} from "../core/HttpMethod";

@Injectable({
  providedIn: 'root'
})
export class ParticipantService {


  $currentParticipantUidSubject = new BehaviorSubject<ParticipantRepresentation>(null)
  currentParticipant$ = this.$currentParticipantUidSubject.asObservable();


  $checkpointsForCurrentParticipant = this.currentParticipant$.pipe(
    mergeMap((part: ParticipantRepresentation) => {
      if (part === null) {
        return [];
      }
      return this.checkpointsForparticipant(part).pipe(
        map((checkpoints: RandonneurCheckPointRepresentation[]) => {
          return checkpoints;
        })
      )

    })
  ) as Observable<RandonneurCheckPointRepresentation[]>;

  constructor(private httpClient: HttpClient, private linkService: LinkService) {
  }

  public participantsForTrack(trackuid: string): Observable<ParticipantRepresentation[]> {
    const path = '/participants/' + trackuid;
    return this.httpClient.get<ParticipantRepresentation[]>(environment.backend_url + path).pipe(
      map((participants: ParticipantRepresentation[]) => {
        return participants;
      })
    ) as Observable<ParticipantRepresentation[]>;
  }

  public participantsForTrackExtended(trackuid: string): Observable<ParticipantInformationRepresentation[]> {
    const path = 'participants/track/' + trackuid + "/extended";
    return this.httpClient.get<ParticipantInformationRepresentation[]>(environment.backend_url + path).pipe(
      map((participants: ParticipantInformationRepresentation[]) => {
        return participants;
      })
    ) as Observable<ParticipantInformationRepresentation[]>;
  }

  public participantsForEvent(eventuid: string) {
    const path = '/participants/' + eventuid;
    return this.httpClient.get<Array<ParticipantRepresentation>>(environment.backend_url + path).pipe(
      take(1),
      map((participants: Array<ParticipantRepresentation>) => {
        return participants;
      }),
      tap((participants: Array<ParticipantRepresentation>) => {
        console.log(participants);
      }),
      shareReplay(1)
    ) as Observable<Array<ParticipantRepresentation>>;
  }

  public checkpointsForparticipant(participant: ParticipantRepresentation): Observable<RandonneurCheckPointRepresentation[]> {
    const link = this.linkService.findByRel(participant.links, 'relation.participant.checkpoints', HttpMethod.GET);
    return this.httpClient.get<RandonneurCheckPointRepresentation[]>(link.url).pipe(
      map((participants: RandonneurCheckPointRepresentation[]) => {
        return participants;
      })
    ) as Observable<RandonneurCheckPointRepresentation[]>;
  }


  async deleteParticipant(participant: ParticipantRepresentation) {
    const link = this.linkService.findByRel(participant.links, 'relation.participant.delete', HttpMethod.DELETE);
    return await this.httpClient.delete(link.url)
      .pipe(
        catchError(err => {
          return throwError(err);
        })
      ).toPromise();
  }

  async setDnf(participant: ParticipantRepresentation) {
    const link = this.linkService.findByRel(participant.links, 'relation.participant.setdnf', HttpMethod.PUT)
    return await this.httpClient.put(link.url, null).pipe(
      catchError(err => {
        return throwError(err);
      })
    ).toPromise()

  }

  async updateTime(participant: ParticipantRepresentation) {
    const link = this.linkService.findByRel(participant.links, 'relation.participant.updatetime', HttpMethod.PUT)
    return await this.httpClient.put(link.url + '?newTime=' + participant.time, null).pipe(
      catchError(err => {
        return throwError(err);
      })
    ).toPromise()

  }

  async rollbackDnf(participant: ParticipantRepresentation) {
    const link = this.linkService.findByRel(participant.links, 'relation.participant.rollbackdnf', HttpMethod.PUT)
    return await this.httpClient.put(link.url, null).pipe(
      catchError(err => {
        return throwError(err);
      })
    ).toPromise();
  }

  async checkinAdmin(checkpoint: RandonneurCheckPointRepresentation) {
    const link = this.linkService.findByRel(checkpoint.links, 'relation.randonneur.admin.stamp', HttpMethod.PUT)
    return await this.httpClient.put(link.url, null).pipe(
      catchError(err => {
        return throwError(err);
      })
    ).toPromise()
  }

  async rollbackcheckinAdmin(checkpoint: RandonneurCheckPointRepresentation) {
    const link = this.linkService.findByRel(checkpoint.links, 'relation.randonneur.admin.stamp.rollback', HttpMethod.PUT)
    return this.httpClient.put(link.url, null).pipe(
      catchError(err => {
        return throwError(err);
      })
    ).toPromise()
  }

  async setDns(participant: ParticipantRepresentation) {
    const link = this.linkService.findByRel(participant.links, 'relation.participant.setdns', HttpMethod.PUT)
    return await this.httpClient.put(link.url, null).pipe(
      catchError(err => {
        return throwError(err);
      })
    ).toPromise();
  }

  async rollbackDns(participant: ParticipantRepresentation) {
    const link = this.linkService.findByRel(participant.links, 'relation.participant.rollbackdns', HttpMethod.PUT)
    return await this.httpClient.put(link.url, null).pipe(
      catchError(err => {
        return throwError(err);
      })
    ).toPromise().then((s) => {
      return s;
    });
  }

  public currentparticipant(currentParticipant: ParticipantRepresentation) {
    this.$currentParticipantUidSubject.next(currentParticipant)
  }

  public removeLinkExists(participant: ParticipantRepresentation): boolean {
    return this.linkService.exists(participant.links, 'relation.participant.delete', HttpMethod.DELETE)
  }

  public dnsLinkExists(participant: ParticipantRepresentation): boolean {
    return this.linkService.exists(participant.links, 'relation.participant.setdns', HttpMethod.PUT)
  }

  public dnfLinkExists(participant: ParticipantRepresentation): boolean {
    return this.linkService.exists(participant.links, 'relation.participant.setdnf', HttpMethod.PUT)
  }

  possibleToDnf$ = this.currentParticipant$.pipe(
    map(resultatStudent => this.linkService.exists(resultatStudent.links, 'relation.participant.setdnf', HttpMethod.PUT))
  );

  possibleToDns$ = this.currentParticipant$.pipe(
    map(resultatStudent => this.linkService.exists(resultatStudent.links, 'relation.participant.setdns', HttpMethod.PUT))
  );

  possibleToUpdateTime$ = this.currentParticipant$.pipe(
    map(resultatStudent => this.linkService.exists(resultatStudent.links, 'relation.participant.updatetime', HttpMethod.PUT))
  );

  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }


  async stamplinkExists(checkpoint: RandonneurCheckPointRepresentation) {
    return await this.linkService.exists(checkpoint.links, 'relation.randonneur.admin.stamp', HttpMethod.PUT)
  }


}
