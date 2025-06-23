import { Injectable } from '@angular/core';
import {BehaviorSubject, combineLatest, forkJoin, merge, Observable, of, Subject, throwError} from "rxjs";
import {User} from "../../shared/api/api";
import {environment} from "../../../environments/environment";
import {catchError, map, shareReplay, startWith, switchMap, tap} from "rxjs/operators";
import { HttpClient } from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class UserService {

  removeSubject = new Subject<string>()
  relaod$ = this.removeSubject.asObservable().pipe(
    startWith(''),
  );

  private userUpdatedSubject = new Subject<User>();
  userUpdatedAction$ = this.userUpdatedSubject.asObservable().pipe(
    startWith(''),
  );

  private refreshSubject = new Subject<void>();
  refresh$ = this.refreshSubject.asObservable().pipe(
    startWith(null),
  );

  allUsers$ = this.refresh$.pipe(
    switchMap(() => this.getAllUSers())
  ) as Observable<User[]>;

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

  usersWithAdd$ = combineLatest([this.allUsers$, this.userInsertedAction$, this.relaod$, this.userUpdatedAction$]).pipe(
    map(([all, insert, del, update]) =>  {
         if(insert){
          return  [...all, insert]
         }
         if(del){
           var index = all.findIndex((elt) => elt.user_uid === del);
           all.splice(index, 1);
           const userArray = all;
           return   this.deepCopyProperties(all);
         }
         if(update && typeof update === 'object' && update.user_uid){
           var index = all.findIndex((elt) => elt.user_uid === update.user_uid);
           if(index >= 0) {
             all[index] = update;
           }
           return this.deepCopyProperties(all);
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

  async updateUserInList(userToUpdate: User) {
    try {
      const updatedUser = await this.updateUser(userToUpdate.user_uid, userToUpdate).toPromise();
      console.log("UserService - Updated user from backend:", updatedUser);

      // Force refresh the users list by triggering a reload
      this.refreshUsersList();

      this.userUpdatedSubject.next(updatedUser);
    } catch (error) {
      console.error('Error updating user:', error);
      throw error;
    }
  }

  private refreshUsersList() {
    // Force refresh by triggering the refresh subject
    this.refreshSubject.next();
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

  public updateUser(useruid: string, user: User): Observable<User>{
    return this.httpClient.put<User>(environment.backend_url + "user/" + useruid, user).pipe(
      map((updatedUser: User) => {
        return updatedUser;
      }),
      tap(updatedUser => console.log("Updated user:", updatedUser))
    );
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
