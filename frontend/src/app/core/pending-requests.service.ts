import {Injectable} from '@angular/core';
import {asapScheduler, BehaviorSubject} from "rxjs";
import {observeOn} from "rxjs/operators";

@Injectable({providedIn: 'root'})
export class PendingRequestsService {

  countSubject = new BehaviorSubject(0);
  count$ = this.countSubject.asObservable().pipe(observeOn(asapScheduler));

    increase() {
      this.countSubject.next(this.countSubject.value + 1);
    }

    decrease() {
      this.countSubject.next(this.countSubject.value + -1);
    }
}
