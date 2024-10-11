import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { environment } from 'src/environments/environment';
import {map} from "rxjs/operators";
import {User} from "../../shared/api/api";
import {Observable} from "rxjs";

@Injectable()
export class UserAdminComponentService {


  $users = this.getAllUSers();


  constructor(private httpClient: HttpClient) {

  }

  private getAllUSers(): Observable<User[]>{
    return this.httpClient.get<User>(environment.backend_url + "users").pipe(
      map((users: any) => {
        console.log(users);
        return users;
    })
    ) as Observable<User[]>
  }

}
