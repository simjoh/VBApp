import {Component, Injector, OnInit, OnDestroy, ChangeDetectionStrategy} from '@angular/core';
import {InititatedService} from "./core/inititated.service";
import {ServiceLocator} from "./core/locator.service";
import {AuthenticatedService} from "./core/auth/authenticated.service";
import {MenuItem, PrimeNGConfig} from "primeng/api";
import {AuthService} from "./core/auth/auth.service";
import {interval, Subscription, Observable} from "rxjs";
import {switchMap, map} from "rxjs/operators";

@Component({
  selector: 'brevet-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class AppComponent implements OnInit, OnDestroy{
  title = 'Västerbottenbrevet';

  items: MenuItem[];
  private tokenValidationSubscription: Subscription;

  init$ = this.initiatedService.initierad$;
  $authenticated = this.authenticatedservice.authenticated$;

  // Check if user is admin
  isAdminUser$: Observable<boolean> = this.authService.$auth$.pipe(
    map(user => {
      if (!user) return false;
      return user.roles.includes('ADMIN') || user.roles.includes('SUPERUSER');
    })
  );

  constructor(
    private primengConfig: PrimeNGConfig,
    injector: Injector,
    private initiatedService: InititatedService,
    private authenticatedservice: AuthenticatedService,
    private authService: AuthService
  ) {
    ServiceLocator.injector = injector;
  }

  logout() {
    this.authService.logoutUser();
  }

  ngOnInit() {
    this.primengConfig.ripple = true;

    // Validate token every 5 minutes
    this.tokenValidationSubscription = interval(5 * 60 * 1000).pipe(
      switchMap(() => this.authService.validateToken())
    ).subscribe();
  }

  ngOnDestroy() {
    if (this.tokenValidationSubscription) {
      this.tokenValidationSubscription.unsubscribe();
    }
  }
}
