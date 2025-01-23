import {Injectable} from '@angular/core';
import {Observable} from "rxjs";
import {EventRepresentation} from "../../shared/api/api";
import {environment} from "../../../environments/environment";
import {map, shareReplay, tap} from "rxjs/operators";
import {HttpClient} from "@angular/common/http";
import {LinkService} from "../../core/link.service";

@Injectable(
  {
    providedIn: 'root'
  }
)
export class AdministrationService {

  constructor(private httpClient: HttpClient, private linkService: LinkService) {
  }

  public getFoundationForAcpReport(): Observable<any[]> {
    return this.httpClient.get<any[]>(environment.backend_url + "acpreport foundation").pipe(
      map((foundations: Array<any>) => {
        return foundations;
      }),
      tap(foundations => console.log("All foundations", foundations)),
      shareReplay(1)
    );
  }

  public getCsvReport(): Observable<any[]> {
    return this.httpClient.get<any[]>(environment.backend_url + "acpreport foundation").pipe(
      map((foundations: Array<any>) => {
        return foundations;
      }),
      tap(foundations => console.log("All foundations", foundations)),
      shareReplay(1)
    );
  }

}
