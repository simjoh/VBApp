import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../../core/link.service";
import {Observable} from "rxjs";
import {Site} from "../../shared/api/api";
import {environment} from "../../../environments/environment";
import {map, shareReplay, tap} from "rxjs/operators";

@Injectable({
  providedIn: 'root'
})
export class OrganizerService {

  constructor(private httpClient: HttpClient, private linkService: LinkService) {

  }




  getAllOrganizers(): Observable<any[]>{
    this.httpClient.get<any[]>(environment.backend_url + "organizers").pipe(
      map((organizers: Array<any>) => {
        return organizers;
      }),
      tap(sites =>   console.log("All sites" ,sites)),
      shareReplay(1)
    ).subscribe();


    return null;
  }
}
