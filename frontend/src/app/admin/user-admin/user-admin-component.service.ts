import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import { environment } from 'src/environments/environment';
import {map} from "rxjs/operators";

@Injectable()
export class UserAdminComponentService {


  $users = this.getAllUSers();


  constructor(private httpClient: HttpClient) {

  }

  private getAllUSers(){


    return this.httpClient.get(environment.backend_url + "users").pipe(
      map((users: any) => {
        console.log(users);
        return users;
    })
    )
  }

}
