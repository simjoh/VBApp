import { Injectable } from '@angular/core';
import {BehaviorSubject, combineLatest, forkJoin, merge, Observable, of, Subject, throwError} from "rxjs";
import {User} from "../../shared/api/api";
import {environment} from "../../../environments/environment";
import {catchError, map, mergeMap, mergeScan, scan, shareReplay, startWith, takeUntil, tap} from "rxjs/operators";
import {HttpClient} from "@angular/common/http";
import {insertAfterLastOccurrence} from "@angular/cdk/schematics";

@Injectable({
  providedIn: 'root'
})
export class UserService {

  removeSubject = new Subject<string>()
  relaod$ = this.removeSubject.asObservable().pipe(
    startWith(''),
  );


  allUsers$ = this.getAllUSers() as Observable<User[]>;

private userInsertedSubject = new Subject<User>();
  userInsertedAction$ = this.userInsertedSubject.asObservable().pipe(
    startWith(''),
  );

  // Merge the streams
  // usersWithAdd$ = merge(
  //   this.allUsers$,
  //   this.userInsertedAction$
  // ).pipe(
  //     scan((acc: any[], value) => [...acc, value]),
  //     catchError(err => {
  //       console.error(err);
  //       return throwError(err);
  //     }),
  //   tap(jj => console.log("All user",jj))
  //   );

  usersWithAdd$ = combineLatest([this.getAllUSers(), this.userInsertedAction$, this.relaod$]).pipe(
    map(([all, insert, del]) =>  {
         if(insert){
          return  [...all, insert]
         }
         if(del){
           var index = all.findIndex((elt) => elt.user_uid === del);
           all.splice(index, 1);
           const userArray = all;
           return   this.deepCopyProperties(all);
         }
         return this.deepCopyProperties(all);
    }),
  );
  $usercount = this.usersWithAdd$.pipe(
    map(users => {
      return users.length
    })
  ) as Observable<Number>;

  constructor(private httpClient: HttpClient) {
  }

 async newUser(newUser: User) {
     const user = await this.addUser(newUser)
    this.userInsertedSubject.next(user);
  }

  private getAllUSers(): Observable<User[]>{
    return this.httpClient.get<User[]>(environment.backend_url + "users").pipe(
      map((users: Array<User>) => {
        return users;
      }),
      tap(users =>   console.log("All users from backen" ,users)),
      shareReplay(1)
    );
  }

  public getUser(userUid: string): Observable<User> {
    return this.httpClient.get<User>(environment.backend_url + "user/" + userUid).pipe(
      map((user: User) => {
        return user;
      }),
      tap(users =>   console.log(users))
    ) as Observable<User>
  }

  async addUser(user1: User){
    return await this.httpClient.post<User>(environment.backend_url + "user/", user1).pipe(
      map((user: User) => {
        return user;
      }),
      tap(users =>   console.log(users))
    ).toPromise();
  }

  public updateUser(useruid: string, user: User){
    return this.httpClient.put<User>(environment.backend_url + "user", {} as User).pipe(
      map((user: User) => {
        return user;
      }),
      tap(users =>   console.log(users))
    ) as Observable<User>
  }

  public deleterUser(userUid: string){
    return this.httpClient.delete(environment.backend_url + "user/" + userUid)
      .pipe(
        catchError(err => {
          return throwError(err);
        })
      ).toPromise().then((s) => {
        this.removeSubject.next(userUid);
      })
  }

    deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }

}
