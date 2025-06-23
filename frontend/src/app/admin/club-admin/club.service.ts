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
      // Handle deletion first (del will be a club_uid string when deletion occurs)
      if (del && del !== '') {
        console.log("Processing deletion for club:", del);
        const index = all.findIndex((elt) => elt.club_uid === del);
        if (index !== -1) {
          console.log("Removing club at index:", index);
          const updatedClubs = [...all];
          updatedClubs.splice(index, 1);
          return this.deepCopyProperties(updatedClubs);
        }
      }

      // Handle insertion
      if (insert && insert !== '') {
        console.log("Processing insertion for club:", insert);
        return [...all, insert]
      }

      // Handle update/reload
      if (clubReload) {
        console.log("Processing update for club:", clubReload.club_uid);
        const indexReload = all.findIndex((elt) => elt.club_uid === clubReload.club_uid);
        if (indexReload !== -1) {
          const updatedClubs = [...all];
          updatedClubs[indexReload] = clubReload;
          return this.deepCopyProperties(updatedClubs);
        }
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
    console.log("Attempting to delete club:", clubUid);
    return this.httpClient.delete(environment.backend_url + "club/" + clubUid)
      .pipe(
        catchError(err => {
          console.error("Error deleting club:", err);
          return throwError(err);
        })
      ).toPromise().then((s) => {
        console.log("Club deletion successful, triggering UI update for:", clubUid);
        this.removeSubject.next(clubUid);
      }).catch((error) => {
        console.error("Club deletion failed:", error);
        throw error;
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
