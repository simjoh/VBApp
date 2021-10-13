import {Injectable} from '@angular/core';
import {Observable, of} from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class InititatedService {

  initierad$ = this.initiated();

  private initiated(): Observable<boolean> {
    return of(true);
  }
}
