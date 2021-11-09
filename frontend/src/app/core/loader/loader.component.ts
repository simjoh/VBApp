import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {NavigationCancel, NavigationEnd, NavigationError, NavigationStart, Router } from '@angular/router';
import { PendingRequestsService } from '../pending-requests.service';
import {BehaviorSubject, combineLatest} from "rxjs";
import { map} from "rxjs/operators";

@Component({
  selector: 'brevet-loader',
  templateUrl: './loader.component.html',
  styleUrls: ['./loader.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class LoaderComponent implements OnInit {
  timer: any = null;


  loadingSubject = new BehaviorSubject(false);
  loading$ = combineLatest([this.loadingSubject.asObservable(), this.pendingRequestsService.count$]).pipe(
    map(([laddar, requestCount]) => {
     return  laddar || requestCount > 0
    })
  );

  constructor(private window: Window,
              private router: Router,
              private pendingRequestsService: PendingRequestsService) { }

  ngOnInit(): void {
    this.router.events.subscribe((event: any) => {
      if (event instanceof NavigationStart) {
        this.loadingSubject.next(true);
        this.cancelTimeout();
        this.timer = this.window.setTimeout(() => {
          this.loadingSubject.next(false);
        }, 20000);
      } else if (event instanceof NavigationEnd || event instanceof NavigationError || event instanceof NavigationCancel) {
        this.cancelTimeout();
        this.loadingSubject.next(false);
      }
    });
  }

  private cancelTimeout() {
    if (this.timer) {
      this.window.clearTimeout(this.timer);
      this.timer = null;
    }
  }

}
