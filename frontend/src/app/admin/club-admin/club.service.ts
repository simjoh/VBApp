import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { combineLatest, Observable, Subject, throwError } from "rxjs";
import { catchError, map, shareReplay, startWith, tap } from "rxjs/operators";
import { ClubRepresentation } from "../../shared/api/api";
import { environment } from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class ClubService {

  removeSubject = new Subject<string>()
  reload$ = this.removeSubject.asObservable().pipe(
    startWith(''),
  );

  clubReloadAction = new Subject<ClubRepresentation>()
  $clubReload = this.clubReloadAction.asObservable().pipe(
    startWith(null),
  );

  allClubs$ = this.getAllClubs() as Observable<ClubRepresentation[]>;

  private clubInsertedSubject = new Subject<ClubRepresentation>();
  clubInsertedAction$ = this.clubInsertedSubject.asObservable().pipe(
    startWith(''),
  );

  clubsWithAdd$ = combineLatest([this.getAllClubs(), this.clubInsertedAction$, this.reload$, this.$clubReload]).pipe(
    map(([all, insert, del, clubReload]) => {
      if (insert) {
        return [...all, insert]
      }
      if (del) {
        var index = all.findIndex((elt) => elt.club_uid === del);
        all.splice(index, 1);
        const clubArray = all;
        return this.deepCopyProperties(all);
      }

      if (clubReload) {
        var indexReload = all.findIndex((elt) => elt.club_uid === clubReload.club_uid);
        all[indexReload] = clubReload;
      }
      return this.deepCopyProperties(all);
    }),
  );

  constructor(private httpClient: HttpClient) { }

  async newClub(newClub: ClubRepresentation) {
    const club = await this.addClub(newClub);
    this.clubInsertedSubject.next(club);
  }

  public getAllClubs(): Observable<ClubRepresentation[]> {
    return this.httpClient.get<ClubRepresentation[]>(environment.backend_url + "club/allclubs").pipe(
      map((clubs: Array<ClubRepresentation>) => {
        return clubs.sort((a, b) => (a.title > b.title) ? 1 : -1);
      }),
      tap(clubs => console.log("All clubs", clubs)),
      shareReplay(1)
    );
  }

  public getClub(clubUid: string): Observable<ClubRepresentation> {
    return this.httpClient.get<ClubRepresentation>(environment.backend_url + "club/" + clubUid).pipe(
      map((club: ClubRepresentation) => {
        return club;
      }),
      tap(club => console.log(club))
    ) as Observable<ClubRepresentation>
  }

  async addClub(club: ClubRepresentation) {
    return await this.httpClient.post<ClubRepresentation>(environment.backend_url + "club/createclub", club).pipe(
      map((club: ClubRepresentation) => {
        return club;
      }),
      tap(club => console.log(club))
    ).toPromise();
  }

  public deleteClub(clubUid: string) {
    return this.httpClient.delete(environment.backend_url + "club/" + clubUid)
      .pipe(
        catchError(err => {
          return throwError(err);
        })
      ).toPromise().then((s) => {
        this.removeSubject.next(clubUid);
      })
  }

  public updateClub(clubUid: string, club: ClubRepresentation) {
    return this.httpClient.put<ClubRepresentation>(environment.backend_url + "club/" + clubUid, club as ClubRepresentation).pipe(
      map((club: ClubRepresentation) => {
        this.clubReloadAction.next(club)
        return club;
      }),
      tap(club => console.log(club))
    ).toPromise()
  }

  deepCopyProperties(obj: any): any {
    // Converts to and from JSON, copies properties but loses methods
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }
}
