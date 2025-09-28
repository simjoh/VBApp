import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class LoppserviceApiService {

  constructor(private httpClient: HttpClient) { }

  /**
   * Generic method to make GET requests to loppservice
   */
  get<T>(endpoint: string): Observable<T> {
    const url = `${environment.loppservice_url}${endpoint}`;
    return this.httpClient.get<T>(url)
      .pipe(
        catchError(err => {
          console.error(`Error making GET request to loppservice ${endpoint}:`, err);
          return throwError(err);
        })
      );
  }

  /**
   * Generic method to make POST requests to loppservice
   */
  post<T>(endpoint: string, data: any): Observable<T> {
    const url = `${environment.loppservice_url}${endpoint}`;
    return this.httpClient.post<T>(url, data)
      .pipe(
        catchError(err => {
          console.error(`Error making POST request to loppservice ${endpoint}:`, err);
          return throwError(err);
        })
      );
  }

  /**
   * Generic method to make PUT requests to loppservice
   */
  put<T>(endpoint: string, data: any): Observable<T> {
    const url = `${environment.loppservice_url}${endpoint}`;
    return this.httpClient.put<T>(url, data)
      .pipe(
        catchError(err => {
          console.error(`Error making PUT request to loppservice ${endpoint}:`, err);
          return throwError(err);
        })
      );
  }

  /**
   * Generic method to make DELETE requests to loppservice
   */
  delete<T>(endpoint: string): Observable<T> {
    const url = `${environment.loppservice_url}${endpoint}`;
    return this.httpClient.delete<T>(url)
      .pipe(
        catchError(err => {
          console.error(`Error making DELETE request to loppservice ${endpoint}:`, err);
          return throwError(err);
        })
      );
  }

  /**
   * Generic method to make PATCH requests to loppservice
   */
  patch<T>(endpoint: string, data: any): Observable<T> {
    const url = `${environment.loppservice_url}${endpoint}`;
    return this.httpClient.patch<T>(url, data)
      .pipe(
        catchError(err => {
          console.error(`Error making PATCH request to loppservice ${endpoint}:`, err);
          return throwError(err);
        })
      );
  }

  /**
   * Method to make requests with custom headers
   */
  request<T>(method: string, endpoint: string, data?: any, customHeaders?: HttpHeaders): Observable<T> {
    const url = `${environment.loppservice_url}${endpoint}`;
    const options = {
      headers: customHeaders
    };

    let request: Observable<T>;
    switch (method.toUpperCase()) {
      case 'GET':
        request = this.httpClient.get<T>(url, options);
        break;
      case 'POST':
        request = this.httpClient.post<T>(url, data, options);
        break;
      case 'PUT':
        request = this.httpClient.put<T>(url, data, options);
        break;
      case 'DELETE':
        request = this.httpClient.delete<T>(url, options);
        break;
      case 'PATCH':
        request = this.httpClient.patch<T>(url, data, options);
        break;
      default:
        throw new Error(`Unsupported HTTP method: ${method}`);
    }

    return request.pipe(
      catchError(err => {
        console.error(`Error making ${method} request to loppservice ${endpoint}:`, err);
        return throwError(err);
      })
    );
  }
}
