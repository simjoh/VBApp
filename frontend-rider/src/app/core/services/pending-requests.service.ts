import { Injectable, signal } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class PendingRequestsService {
  private _count = signal(0);

  // Read-only signal for components
  readonly count$ = this._count.asReadonly();

  increase(): void {
    this._count.update(count => count + 1);
  }

  decrease(): void {
    this._count.update(count => Math.max(0, count - 1));
  }

  get count(): number {
    return this._count();
  }
}
