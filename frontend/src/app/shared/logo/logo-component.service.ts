import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {map} from "rxjs/operators";

@Injectable()
export class LogoComponentService {

  $logo = this.getJSON();

  constructor(private httpclient: HttpClient) {
  }

  public getJSON(): Observable<any> {
    return this.httpclient.get("./assets/logo.json").pipe(
      map(log => {
        return log;
    })
    );
  }
}
