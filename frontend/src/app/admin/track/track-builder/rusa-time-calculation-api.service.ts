import {Injectable} from '@angular/core';
import { HttpClient, HttpHeaders } from "@angular/common/http";
import {SiteRepresentation} from "../../../shared/api/api";
import {catchError, map, scan, tap} from "rxjs/operators";
import {
  RusaControlResponseRepresentation,
  RusaPlannerControlInputRepresentation,
  RusaPlannerInputRepresentation, RusaPlannerResponseRepresentation,
  RusaTimeRepresentation
} from "../../../shared/api/rusaTimeApi";
import {environment} from "../../../../environments/environment";
import {BehaviorSubject, merge, throwError} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class RusaTimeCalculationApiService {

  rusaTimeSubject = new BehaviorSubject<RusaTimeRepresentation>({} as RusaTimeRepresentation);
  $rusaTime = this.rusaTimeSubject.asObservable();

  rusaTimeImputSubject = new BehaviorSubject({} as RusaPlannerInputRepresentation);
  rusaTimeInput = this.rusaTimeImputSubject.asObservable();


  rusaTimeControlsSubject = new BehaviorSubject({} as RusaPlannerControlInputRepresentation);
  rusaTimeControls$ = this.rusaTimeControlsSubject.asObservable();

  rusaTimeControlAddSubject = new BehaviorSubject({} as RusaPlannerControlInputRepresentation);
  rusaTimeControlAdd$ = this.rusaTimeControlAddSubject.asObservable();

  rusaControls$ = merge(
    this.rusaTimeControls$,
    this.rusaTimeControlAdd$
  ).pipe(
    scan((acc: RusaPlannerControlInputRepresentation[], value: RusaPlannerControlInputRepresentation) => [...acc, value]),
    catchError(err => {
      console.error(err);
      return throwError(err);
    })
  );







  constructor(private httpClient: HttpClient) {
  }


  addSite(rusaPlannerInputRepresentation: RusaPlannerInputRepresentation) {
    const httpOptions = {
      headers: new HttpHeaders()
    }
    httpOptions.headers.append('Access-Control-Allow-Origin', '*');
    console.log('API Service calling trackplanner with:', rusaPlannerInputRepresentation);
    return this.httpClient.post<RusaPlannerResponseRepresentation>(environment.backend_url + 'trackplanner', rusaPlannerInputRepresentation, httpOptions).pipe(
      map((site: RusaPlannerResponseRepresentation) => {
        console.log('API Service received response:', site);
        return site;
      }),
      tap(site => console.log('API Service tap response:', site))
    );
  }


  addControl(newProduct?: RusaPlannerControlInputRepresentation): void {
    this.rusaTimeControlAddSubject.next(newProduct);
  }

  addTrackInfo(): void {

  }


// ?controls=%5B%7B%22DISTANCE%22%3A%220km%22%2C%22NAME%22%3A%22BROPARKEN%22%7D%2C%7B%22DISTANCE%22%3A%22129km%22%2C%22NAME%22%3A%22Random%20Store%22%7D%2C%7B%22DISTANCE%22%3A%22205km%22%2C%22NAME%22%3A%22Volunteer%22%7D%2C%7B%22DISTANCE%22%3A%22411km%22%2C%22NAME%22%3A%22Stadium%20Control%22%7D%2C%7B%22DISTANCE%22%3A%22555km%22%2C%22NAME%22%3A%22All%20Fives%22%7D%5D&event_distance=375&gravel_distance=325km&start_date=July%204th%202027&start_time=8%3A52pm


  private testobject() {

    // let controlarray: Array<RusaPlannerControlInputRepresentation>;

    let controlarray = [];

    const contr = {
      DISTANCE: 150,
      SITE: ""
    } as RusaPlannerControlInputRepresentation


    controlarray.push({
      "DISTANCE": "0km",
      "NAME": "Broparken start"
    }, {
      "DISTANCE": "28km",
      "NAME": "Brännäset bryggkafe"
    }, {
      "DISTANCE": "45km",
      "NAME": "Rodtjarn"
    }, {
      "DISTANCE": "100km",
      "NAME": "Rödåsel"
    }, {
      "DISTANCE": "200km",
      "NAME": "Broparken mål"
    })


    let s = [{
      "DISTANCE": "0km",
      "SITE_UID": "Broparken start"
    }, {
      "DISTANCE": "28km",
        "SITE_UID": "Brännäset bryggkafe"
    }, {
      "DISTANCE": "45km",
        "SITE_UID": "Rodtjarn"
    }, {
      "DISTANCE": "100km",
        "SITE_UID": "Rödåsel"
    }, {
      "DISTANCE": "200km",
        "SITE_UID": "1232-22"
    }]



    // controlarray.push(contr)

    const pay = {
      controls: controlarray,
      event_distance: 300,
      start_date: "2022-08-20",
      start_time: "08:00",
    } as RusaPlannerInputRepresentation

    // const payload = {
    //
    //   "controls": [{
    //     "DISTANCE": "0km",
    //     "NAME": "Broparken start"
    //   },{
    //     "DISTANCE": "28km",
    //     "NAME": "Brännäset bryggkafe"
    //   }, {
    //     "DISTANCE": "45km",
    //     "NAME": "Rodtjarn"
    //   }, {
    //     "DISTANCE": "100km",
    //     "NAME": "Rödåsel"
    //   }, {
    //     "DISTANCE": "200km",
    //     "NAME": "Broparken mål"
    //   }],
    //   "event_distance": "200km",
    //   "start_time": "08:00",
    //   "start_date": "2022-08-20",
    //   "event_uid": "123"
    //
    // }

    return pay;
  }
}



