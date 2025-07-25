import {Injectable} from '@angular/core';
import {combineLatest, Observable, of, Subject, throwError} from "rxjs";
import {catchError, map, mergeMap, shareReplay, startWith, tap} from "rxjs/operators";

import { HttpClient } from "@angular/common/http";
import {EventRepresentation, ParticipantInformationRepresentation} from 'src/app/shared/api/api';
import {environment} from 'src/environments/environment';
import {LinkService} from "../../../core/link.service";


@Injectable({
  providedIn: 'root'
})
export class EventService {

  removeSubject = new Subject<string>()
  relaod$ = this.removeSubject.asObservable().pipe(
    startWith(''),
  );

  allEvents$ = this.getAllEvents() as Observable<EventRepresentation[]>;

  private userInsertedSubject = new Subject<EventRepresentation>();
  userInsertedAction$ = this.userInsertedSubject.asObservable().pipe(
    startWith(''),
  );


  eventsWithAdd$ = combineLatest([this.getAllEvents(), this.userInsertedAction$, this.relaod$]).pipe(
    map(([all, insert, del]) => {
      if (insert) {
        return [...all, insert]
      }
      if (del) {
        var index = all.findIndex((elt) => elt.event_uid === del);
        all.splice(index, 1);
        const userArray = all;
        return this.deepCopyProperties(all);
      }
      return this.deepCopyProperties(all);
    }),
  );

  constructor(private httpClient: HttpClient, private linkService: LinkService) {
  }


  async newEvent(newSite: EventRepresentation) {
    const user = await this.addSite(newSite);
    this.userInsertedSubject.next(user);
  }

  private getAllEvents(): Observable<EventRepresentation[]> {
    return this.httpClient.get<EventRepresentation[]>(environment.backend_url + "events").pipe(
      map((events: Array<EventRepresentation>) => {
        return events;
      }),
      shareReplay(1)
    );
  }

  // public getEvent(eventUid: string){
  //
  //   this.httpClient.get<EventRepresentation>(environment.backend_url + "event/" + eventUid).pipe(
  //     map((rs:EventRepresentation) => {
  //       return rs;
  //     }),
  //     shareReplay(1),
  //   ) as Observable<EventRepresentation>;
  // }

    getEvent(eventUid: string): Observable<EventRepresentation> {
           return this.httpClient.get<EventRepresentation>(environment.backend_url + "event/" + eventUid)
  }

  async addSite(event: EventRepresentation) {
    return await this.httpClient.post<EventRepresentation>(environment.backend_url + "event/", event).pipe(
      map((site: EventRepresentation) => {
        return event;
      })
    ).toPromise();
  }

  public deleterEvent(eventUid: string) {
    return this.httpClient.delete(environment.backend_url + "event/" + eventUid)
      .pipe(
        catchError(err => {
          return throwError(err);
        })
      ).toPromise().then((s) => {
        this.removeSubject.next(eventUid);
      })
  }

  public deleterEvent2(eventUid: string) {
    return this.httpClient.delete(environment.backend_url + "event/" + eventUid)
      .pipe(
        catchError(err => {
          return throwError(err);
        })
      ).toPromise()
  }

  public updateEvent(eventuid: string, event: EventRepresentation) {
    return this.httpClient.put<EventRepresentation>(environment.backend_url + "event", {} as EventRepresentation).pipe(
      map((event: EventRepresentation) => {
        return event;
      })
    ) as Observable<EventRepresentation>
  }

  public deletelinkExists(event: EventRepresentation): boolean {
    if (!event || !event.links) {
      return false;
    }
    const exists = this.linkService.exists(event.links, 'relation.event.delete', 'DELETE');

    return exists;
  }

  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }
}
