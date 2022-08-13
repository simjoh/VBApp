import { Injectable } from '@angular/core';
import {HttpClient, HttpParams} from "@angular/common/http";
import {Site} from "../../../shared/api/api";
import {map, tap} from "rxjs/operators";
import {RusaTimeRepresentation} from "../../../shared/api/rusaTimeApi";

@Injectable({
  providedIn: 'root'
})
export class RusaTimeCalculationApiService {

  constructor(private httpClient: HttpClient) { }


  addSite(site: Site){
   return  this.httpClient.get<RusaTimeRepresentation>('/rusatime?controls=%5B%7B%22DISTANCE%22%3A%220km%22%2C%22NAME%22%3A%22BROPARKEN%22%7D%2C%7B%22DISTANCE%22%3A%22129km%22%2C%22NAME%22%3A%22Random%20Store%22%7D%2C%7B%22DISTANCE%22%3A%22205km%22%2C%22NAME%22%3A%22Volunteer%22%7D%2C%7B%22DISTANCE%22%3A%22411km%22%2C%22NAME%22%3A%22Stadium%20Control%22%7D%2C%7B%22DISTANCE%22%3A%22555km%22%2C%22NAME%22%3A%22All%20Fives%22%7D%5D&event_distance=375&gravel_distance=325km&start_date=July%204th%202027&start_time=8%3A52pm', ).pipe(
      map((site: RusaTimeRepresentation) => {
        return site;
      }),
      tap(site =>   console.log(site))
    );
  }



  private buildUrl(){

    const obj: any = {
      'number': 123,
      'string': 'lincoln',
      'empty': null,
      'bool': true,
      'array': ['1991', null, 'ramanujan'],
    }

   let  params = new HttpParams()
      .set('page', obj)
      .set('page', '3')
      .set('sort', 'name');
  }
}
