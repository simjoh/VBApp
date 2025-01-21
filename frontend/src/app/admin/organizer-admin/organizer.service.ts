import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../../core/link.service";
import {combineLatest, Observable, Subject} from "rxjs";
import {environment} from "../../../environments/environment";
import {map, shareReplay, startWith, tap} from "rxjs/operators";
import {EventRepresentation, OrganizerRepresentation} from "../../shared/api/api";

@Injectable({
  providedIn: 'root'
})
export class OrganizerService {


  allOrganizers$ = this.getAllOrganizers() as Observable<OrganizerRepresentation[]>;

  private organizerInsertedInsertedSubject = new Subject<OrganizerRepresentation>();
  organizerInserted$ = this.organizerInsertedInsertedSubject.asObservable().pipe(
    startWith(''),
  );

  removeSubject = new Subject<string>()
  relaod$ = this.removeSubject.asObservable().pipe(
    startWith(''),
  );

  organizerReloadAction = new Subject<OrganizerRepresentation>()
  $organizerReload = this.organizerReloadAction.asObservable().pipe(
    startWith(null),
  );


  eventsWithAdd$ = combineLatest([this.getAllOrganizers(), this.organizerInserted$, this.relaod$, this.$organizerReload]).pipe(
    map(([all, insert, del, eventreload]) => {
      if (insert) {
        return [...all, insert]
      }
      if (del) {
        var index = all.findIndex((elt) => elt.organizer_id === Number(del));
        all.splice(index, 1);
        const userArray = all;
        return this.deepCopyProperties(all);
      }

      if (eventreload) {
        var indexreload = all.findIndex((elt) => elt.organizer_id === eventreload.organizer_id);
        all[indexreload] = eventreload;

      }
      return this.deepCopyProperties(all);
    }),
  );

  constructor(private httpClient: HttpClient, private linkService: LinkService) {

  }


  getAllOrganizers(): Observable<OrganizerRepresentation[]> {
    return this.httpClient.get<OrganizerRepresentation[]>(environment.backend_url + "organizers").pipe(
      map((organizers: Array<OrganizerRepresentation>) => {
        return organizers;
      }),
      tap(sites => console.log("All organizers", sites)),
      shareReplay(1)
    );
  }


  async addSite(event: OrganizerRepresentation) {
    console.log(event);

    return await this.httpClient.post<OrganizerRepresentation>(environment.backend_url + "organizers", event).pipe(
      map((organizer: OrganizerRepresentation) => {
        return organizer;
      }),
      tap(organizer => console.log(organizer))
    ).toPromise();
  }


  async newOrganizer(newSite: OrganizerRepresentation) {
    const user = await this.addSite(newSite);
    this.organizerInsertedInsertedSubject.next(user);
  }


  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }


}
