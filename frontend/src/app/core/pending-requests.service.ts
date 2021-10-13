import {Injectable} from '@angular/core';

@Injectable({providedIn: 'root'})
export class PendingRequestsService {
    count = 0;

  // tslint:disable-next-line:typedef
    increase() {
        this.count++;
    }

  // tslint:disable-next-line:typedef
    decrease() {
        this.count--;
    }
}
