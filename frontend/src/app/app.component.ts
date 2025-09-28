import {Component, Injector, OnInit, OnDestroy, ChangeDetectionStrategy} from '@angular/core';
import {InititatedService} from "./core/inititated.service";
import {ServiceLocator} from "./core/locator.service";
import {AuthenticatedService} from "./core/auth/authenticated.service";
import {MenuItem, PrimeNGConfig} from "primeng/api";
import {AuthService} from "./core/auth/auth.service";
import {interval, Subscription, Observable} from "rxjs";
import {switchMap, map} from "rxjs/operators";
import {LanguageService} from "./core/services/language.service";
import {SidebarService, SidebarMenuItem} from "./core/sidebar/sidebar.service";

@Component({
  selector: 'brevet-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
  changeDetection: ChangeDetectionStrategy.Default
})
export class AppComponent implements OnInit, OnDestroy{
  title = 'Västerbottenbrevet';

  items: MenuItem[];
  private tokenValidationSubscription: Subscription;

  init$ = this.initiatedService.initierad$;
  $authenticated = this.authenticatedservice.authenticated$;

  // Check if user is admin
  isAdminUser$: Observable<boolean> = this.authenticatedservice.authenticated$.pipe(
    map(isAuthenticated => {
      if (!isAuthenticated) return false;

      const userData = localStorage.getItem("activeUser");
      if (userData) {
        try {
          const parsedUser = JSON.parse(userData);
          return parsedUser.roles.includes('ADMIN') || parsedUser.roles.includes('SUPERUSER');
        } catch (e) {
          return false;
        }
      }
      return false;
    })
  );

  // Check if user is volunteer
  isVolunteerUser$: Observable<boolean> = this.authenticatedservice.authenticated$.pipe(
    map(isAuthenticated => {
      if (!isAuthenticated) return false;

      const userData = localStorage.getItem("activeUser");
      if (userData) {
        try {
          const parsedUser = JSON.parse(userData);
          return parsedUser.roles.includes('VOLONTEER');
        } catch (e) {
          return false;
        }
      }
      return false;
    })
  );

  // Check if user should see top menu (only competitors) - COMMENTED OUT
  // shouldShowTopMenu$: Observable<boolean> = this.authenticatedservice.authenticated$.pipe(
  //   map(isAuthenticated => {
  //     if (!isAuthenticated) return false;

  //     const userData = localStorage.getItem("activeUser");
  //     if (userData) {
  //       try {
  //         const parsedUser = JSON.parse(userData);
  //         const roles = parsedUser.roles || [];

  //         // Only show top menu for competitors (users who are not admin, superuser, or volunteer)
  //         return !roles.includes('ADMIN') &&
  //                !roles.includes('SUPERUSER') &&
  //                !roles.includes('VOLONTEER');
  //       } catch (e) {
  //         return false;
  //       }
  //     }
  //     return false;
  //   })
  // );

  // Sidebar visibility and menu items
  shouldShowSidebar$ = this.sidebarService.shouldShowSidebar();
  sidebarMenuItems$ = this.sidebarService.getMenuItemsForCurrentUser();
  sidebarOpen$ = this.sidebarService.sidebarOpen$;

  constructor(
    private primengConfig: PrimeNGConfig,
    injector: Injector,
    private initiatedService: InititatedService,
    private authenticatedservice: AuthenticatedService,
    private authService: AuthService,
    private languageService: LanguageService,
    private sidebarService: SidebarService
  ) {
    ServiceLocator.injector = injector;
  }

  logout() {
    this.authService.logoutUser();
  }

  toggleSidebar() {
    this.sidebarService.toggleSidebar();
  }

  ngOnInit() {
    this.primengConfig.ripple = true;

    // Initialize language service
    try {
      this.languageService.setLanguage(this.languageService.getCurrentLanguage());
    } catch (error) {
      // Language service initialization failed
    }

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
