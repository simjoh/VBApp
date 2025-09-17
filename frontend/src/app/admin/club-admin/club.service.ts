import { Injectable, OnDestroy } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { combineLatest, Observable, Subject, throwError } from "rxjs";
import { catchError, map, shareReplay, startWith, tap, takeUntil, debounceTime } from "rxjs/operators";
import { ClubRepresentation } from "../../shared/api/api";
import { environment } from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class ClubService implements OnDestroy {
  private destroy$ = new Subject<void>();

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
    debounceTime(100), // Add debouncing to prevent excessive updates
    map(([all, insert, del, clubReload]) => {
      // Handle deletion first (del will be a club_uid string when deletion occurs)
      if (del && del !== '') {
        const index = all.findIndex((elt) => elt.club_uid === del);
        if (index !== -1) {
          const updatedClubs = [...all];
          updatedClubs.splice(index, 1);
          return this.deepCopyProperties(updatedClubs);
        }
      }

      // Handle insertion
      if (insert && insert !== '') {
        return [...all, insert]
      }

      // Handle update/reload
      if (clubReload) {
        const indexReload = all.findIndex((elt) => elt.club_uid === clubReload.club_uid);
        if (indexReload !== -1) {
          const updatedClubs = [...all];
          updatedClubs[indexReload] = clubReload;
          return this.deepCopyProperties(updatedClubs);
        }
      }

      return this.deepCopyProperties(all);
    }),
    takeUntil(this.destroy$)
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
      shareReplay(1)
    );
  }

  public getClub(clubUid: string): Observable<ClubRepresentation> {
    return this.httpClient.get<ClubRepresentation>(environment.backend_url + "club/" + clubUid).pipe(
      map((club: ClubRepresentation) => {
        return club;
      })
    ) as Observable<ClubRepresentation>
  }

  async addClub(club: ClubRepresentation) {
    return await this.httpClient.post<ClubRepresentation>(environment.backend_url + "club/createclub", club).pipe(
      map((response: ClubRepresentation) => {
        return response;
      })
    ).toPromise();
  }

  public deleteClub(clubUid: string) {
    return this.httpClient.delete(environment.backend_url + "club/" + clubUid)
      .pipe(
        catchError(err => {
          console.error("Error deleting club:", err);
          return throwError(err);
        })
      ).toPromise().then((s) => {
        this.removeSubject.next(clubUid);
      }).catch((error) => {
        console.error("Club deletion failed:", error);
        throw error;
      })
  }

  public updateClub(clubUid: string, club: ClubRepresentation) {
    return this.httpClient.put<ClubRepresentation>(environment.backend_url + "club/" + clubUid, club as ClubRepresentation).pipe(
      map((response: ClubRepresentation) => {
        this.clubReloadAction.next(response)
        return response;
      })
    ).toPromise()
  }

  deepCopyProperties(obj: any): any {
    // Converts to and from JSON, copies properties but loses methods
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
