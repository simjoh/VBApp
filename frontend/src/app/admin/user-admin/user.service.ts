import { Injectable } from '@angular/core';
import {merge, Observable, of, Subject, throwError} from "rxjs";
import {User} from "../../shared/api/api";
import {environment} from "../../../environments/environment";
import {catchError, map, mergeScan, scan, shareReplay, tap} from "rxjs/operators";
import {HttpClient} from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class UserService {


  allUsers$ = this.getAllUSers() as Observable<User[]>;

  $usercount = this.allUsers$.pipe(
    map(users => {
      return users.length
    })
  ) as Observable<Number>;

  private userInsertedSubject = new Subject<User>();
  userInsertedAction$ = this.userInsertedSubject.asObservable();

  // Merge the streams
  usersWithAdd$ = merge(
    this.allUsers$,
    this.userInsertedAction$
  ).pipe(
      scan((acc: any[], value) => [...acc, value]),
      catchError(err => {
        console.error(err);
        return throwError(err);
      }),
    tap(jj => console.log("TEST",jj))
    );

  constructor(private httpClient: HttpClient) {
  }

 async newUser(newUser: User) {
     const user = await this.addUser(this.createUserObject())
    this.userInsertedSubject.next(user);
  }

  private getAllUSers(): Observable<User[]>{
    return this.httpClient.get<User[]>(environment.backend_url + "users").pipe(
      map((users: Array<User>) => {
        return users;
      }),
      tap(users =>   console.log(users)),
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

  async  addUser(user1: User){
    return await this.httpClient.post<User>(environment.backend_url + "user/", user1).pipe(
      map((user: User) => {
        return user;
      }),
      tap(users =>   console.log(users))
    ).toPromise();
  }

  public updateUser(useruid: string){
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
      )
  }


  private createUserObject(){
    return {
      user_uid: "",
      givenname: "Kalle",
      familyname: "Cyklist",
      username: "kalle@cyklist",
      token: "",
      roles: ["COMPETITOR"]
    } as User;
  }


}
