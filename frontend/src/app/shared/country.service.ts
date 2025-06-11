import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { environment } from '../../environments/environment';

export interface Country {
  country_id: number;
  country_name_en: string;
  country_name_sv: string;
  country_code: string;
  flag_url_svg?: string;
  flag_url_png?: string;
  created_at?: string;
  updated_at?: string;
}

@Injectable({
  providedIn: 'root'
})
export class CountryService {

  constructor(private httpClient: HttpClient) { }

  /**
   * Get all countries
   */
  getAllCountries(): Observable<Country[]> {
    const url = `${environment.backend_url}countries`;
    return this.httpClient.get<Country[]>(url)
      .pipe(
        catchError(err => {
          console.error('Error getting countries:', err);
          return throwError(err);
        })
      );
  }
}
