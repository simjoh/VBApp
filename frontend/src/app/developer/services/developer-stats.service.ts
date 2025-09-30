import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, forkJoin, of } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';

export interface DeveloperStats {
  publishedEvents: number;
  errorEvents: number;
  trafficLightStatus: 'green' | 'yellow' | 'red';
  lastUpdated: string;
}

@Injectable({
  providedIn: 'root'
})
export class DeveloperStatsService {

  constructor(private http: HttpClient) { }

  private getJwtToken(): string | null {
    return localStorage.getItem('token') || sessionStorage.getItem('token') || null;
  }

  getStats(): Observable<DeveloperStats> {
    const token = this.getJwtToken();

    // Headers for main API
    const mainApiHeaders = new HttpHeaders({
      'APIKEY': 'notsecret_developer_key',
      'Content-Type': 'application/json',
      ...(token && { 'TOKEN': token })
    });

    // Headers for loppservice
    const loppserviceHeaders = new HttpHeaders({
      'apikey': 'testkey',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token && { 'TOKEN': token })
    });

    const apiUrl = environment.loppservice_url || '/loppservice/';

    // Fetch published events from loppservice
    const publishedEvents$ = this.http.get<any>(`${apiUrl}api/integration/published-events-count`, { headers: loppserviceHeaders })
      .pipe(
        map(response => response.count || response.data?.count || 0),
        catchError(() => of(0))
      );

    // Fetch error events from loppservice
    const errorEvents$ = this.http.get<any>(`${apiUrl}api/integration/error-events?limit=1000`, { headers: loppserviceHeaders })
      .pipe(
        map(response => response.data?.length || 0),
        catchError(() => of(0))
      );

    return forkJoin({
      publishedEvents: publishedEvents$,
      errorEvents: errorEvents$
    }).pipe(
      map(({ publishedEvents, errorEvents }) => {
        let trafficLightStatus: 'green' | 'yellow' | 'red' = 'green';

        if (errorEvents >= 10) {
          trafficLightStatus = 'red';
        } else if (errorEvents >= 3) {
          trafficLightStatus = 'yellow';
        }

        return {
          publishedEvents,
          errorEvents,
          trafficLightStatus,
          lastUpdated: new Date().toISOString()
        };
      })
    );
  }
}
