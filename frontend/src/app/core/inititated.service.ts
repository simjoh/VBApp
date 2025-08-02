import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { catchError, map, tap } from 'rxjs/operators';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class InititatedService {

  constructor(private http: HttpClient) {}

  initierad$ = this.initiated();

  private initiated(): Observable<boolean> {
    const token = localStorage.getItem('loggedInUser');
    
    if (!token) {
      return of(true);
    }

    // Validate token by making a simple API call
    return this.http.get(`${environment.backend_url}ping`, { 
      headers: { 
        'APIKEY': environment.api_key,
        'TOKEN': token 
      } 
    }).pipe(
      map(() => true),
      catchError(() => {
        // Token is invalid, clear it
        localStorage.removeItem('loggedInUser');
        localStorage.removeItem('activeUser');
        return of(true);
      })
    );
  }
}
