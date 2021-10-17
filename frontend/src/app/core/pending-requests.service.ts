import {Injectable} from '@angular/core';

@Injectable({providedIn: 'root'})
export class PendingRequestsService {
    count = 0;

    increase() {
        this.count++;
    }

    decrease() {
        this.count--;
    }
}
