import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface CompetitorInfo {
  email: string;
  phone: string;
  adress: string;
  postal_code: string;
  place: string;
  country: string;
  country_id: number;
}

@Injectable({
  providedIn: 'root'
})
export class CompetitorInfoService {

  constructor(private httpClient: HttpClient) { }

  /**
   * Get competitor info by competitor UID
   */
  getCompetitorInfo(competitorUid: string): Observable<CompetitorInfo> {
    const url = `${environment.backend_url}competitor/${competitorUid}/info`;
    return this.httpClient.get<CompetitorInfo>(url)
      .pipe(
        catchError(err => {
          console.error('Error getting competitor info:', err);
          return throwError(err);
        })
      );
  }

  /**
   * Update competitor info using JSON body
   */
  updateCompetitorInfo(competitorUid: string, competitorInfo: CompetitorInfo): Observable<CompetitorInfo> {
    const url = `${environment.backend_url}competitor/${competitorUid}/info`;
    return this.httpClient.put<CompetitorInfo>(url, competitorInfo)
      .pipe(
        catchError(err => {
          console.error('Error updating competitor info:', err);
          return throwError(err);
        })
      );
  }

  /**
   * Create new competitor info
   */
  createCompetitorInfo(competitorUid: string, competitorInfo: CompetitorInfo): Observable<CompetitorInfo> {
    const url = `${environment.backend_url}competitor/${competitorUid}/info`;
    return this.httpClient.post<CompetitorInfo>(url, competitorInfo)
      .pipe(
        catchError(err => {
          console.error('Error creating competitor info:', err);
          return throwError(err);
        })
      );
  }

  /**
   * Update competitor info using query parameters
   */
  updateCompetitorInfoParams(competitorUid: string, params: Partial<CompetitorInfo>): Observable<CompetitorInfo> {
    const url = `${environment.backend_url}competitor/${competitorUid}/info/params`;
    return this.httpClient.patch<CompetitorInfo>(url, null, { params: params as any })
      .pipe(
        catchError(err => {
          console.error('Error updating competitor info with params:', err);
          return throwError(err);
        })
      );
  }
}
