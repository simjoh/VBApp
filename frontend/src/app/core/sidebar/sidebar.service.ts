import { Injectable, effect } from '@angular/core';
import { Observable, BehaviorSubject } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { AuthService } from '../auth/auth.service';
import { AuthenticatedService } from '../auth/authenticated.service';
import { Role } from '../auth/roles';
import { LanguageService } from '../services/language.service';

export interface SidebarMenuItem {
  label: string;
  routerLink?: string;
  icon?: string;
  children?: SidebarMenuItem[];
  roles?: string[];
  cssClass?: string;
}

@Injectable({
  providedIn: 'root'
})
export class SidebarService {
  private menuItemsSubject = new BehaviorSubject<SidebarMenuItem[]>([]);
  public menuItems$ = this.menuItemsSubject.asObservable();

  private sidebarOpenSubject = new BehaviorSubject<boolean>(false);
  public sidebarOpen$ = this.sidebarOpenSubject.asObservable();

  constructor(
    private authService: AuthService,
    private authenticatedService: AuthenticatedService,
    private languageService: LanguageService
  ) {
    // Initialize immediately and listen for changes
    this.initializeMenuItems();
    this.listenForAuthChanges();
    this.listenForLanguageChanges();
  }

  private getInitialMenuItems(): SidebarMenuItem[] {
    // Get initial menu items from localStorage immediately
    const userData = localStorage.getItem("activeUser");

    if (userData) {
      try {
        const user = JSON.parse(userData);
        if (user && user.roles) {
          const menuItems = this.buildMenuItemsForUser(user.roles);
          return menuItems;
        }
      } catch (error) {
        console.error('Error parsing user data from localStorage:', error);
      }
    }

    return [];
  }

  private initializeMenuItems(): void {
    // Always try to initialize menu items from localStorage
    // This ensures immediate loading if user data is available
    const initialMenuItems = this.getInitialMenuItems();
    this.menuItemsSubject.next(initialMenuItems);
  }

  private listenForAuthChanges(): void {
    // Listen for authentication changes and update menu items
    this.authenticatedService.authenticated$.subscribe(isAuthenticated => {
      console.log('SidebarService: Auth state changed - isAuthenticated:', isAuthenticated);

      if (isAuthenticated) {
        // User is authenticated, update menu items
        // Add a small delay to ensure localStorage is updated
        setTimeout(() => {
          this.checkUserFromStorage();
        }, 100);

        // Also check again after a longer delay to catch any late updates
        setTimeout(() => {
          this.checkUserFromStorage();
        }, 500);
      } else {
        // User logged out, clear menu items
        console.log('SidebarService: User logged out, clearing menu items');
        this.menuItemsSubject.next([]);
      }
    });
  }

  private listenForLanguageChanges(): void {
    // Listen for language changes using effect
    effect(() => {
      // This will run whenever the language signal changes
      const currentLanguage = this.languageService.currentLanguage();
      console.log('SidebarService: Language changed to:', currentLanguage);

      // Only refresh if user is authenticated
      if (this.authenticatedService.authenticatedSubject.value) {
        console.log('SidebarService: Refreshing menu items due to language change');
        this.checkUserFromStorage();
      }
    });
  }

  private checkUserFromStorage(): void {
    const userData = localStorage.getItem("activeUser");
    console.log('SidebarService: checkUserFromStorage - userData:', userData ? 'Found' : 'Not found');

    if (userData) {
      try {
        const user = JSON.parse(userData);
        console.log('SidebarService: checkUserFromStorage - user:', user);

        if (user && user.roles) {
          const menuItems = this.buildMenuItemsForUser(user.roles);
          console.log('SidebarService: checkUserFromStorage - built menu items:', menuItems);
          this.menuItemsSubject.next(menuItems);
        } else {
          console.log('SidebarService: checkUserFromStorage - no roles found');
          this.menuItemsSubject.next([]);
        }
      } catch (error) {
        console.error('Error parsing user data from localStorage:', error);
        this.menuItemsSubject.next([]);
      }
    } else {
      console.log('SidebarService: checkUserFromStorage - no user data found');
      this.menuItemsSubject.next([]);
    }
  }

  private buildMenuItemsForUser(userRoles: string[]): SidebarMenuItem[] {
    const menuItems: SidebarMenuItem[] = [];

    // Check if user is admin or superuser
    const isAdmin = userRoles.includes(Role.ADMIN) || userRoles.includes(Role.SUPERUSER);
    const isSuperUser = userRoles.includes(Role.SUPERUSER);
    const isVolunteer = userRoles.includes(Role.VOLONTEER);

    console.log('SidebarService: Building menu for roles:', userRoles);
    console.log('SidebarService: isAdmin:', isAdmin, 'isSuperUser:', isSuperUser, 'isVolunteer:', isVolunteer);

    if (isAdmin) {
      // Admin menu items
      menuItems.push(
        {
          label: 'nav.home',
          routerLink: '/admin/brevet-admin-start',
          icon: 'pi pi-home'
        },
        {
          label: 'nav.participants',
          icon: 'pi pi-users',
          children: [
            {
              label: 'nav.participantList',
              routerLink: '/admin/participant',
              icon: 'pi pi-list'
            },
            {
              label: 'nav.uploadParticipants',
              routerLink: '/admin/participant/brevet-participant-upload',
              icon: 'pi pi-upload'
            }
          ]
        },
        {
          label: 'nav.tracks',
          icon: 'pi pi-map',
          children: [
            {
              label: 'nav.trackList',
              routerLink: '/admin/banor',
              icon: 'pi pi-list'
            },
            {
              label: 'nav.createNewEvent',
              routerLink: '/admin/banor/brevet-track-create',
              icon: 'pi pi-plus-circle'
            },
            {
              label: 'nav.gpxImport',
              routerLink: '/admin/banor/brevet-track-gpx-import',
              icon: 'pi pi-file',
              cssClass: 'coming-soon'
            },
            {
              label: 'nav.copyTrack',
              routerLink: '/admin/banor/brevet-track-copy',
              icon: 'pi pi-copy',
              cssClass: 'coming-soon'
            }
          ]
        },
        {
          label: 'nav.reports',
          icon: 'pi pi-file-pdf',
          children: [
            {
              label: 'nav.reportToAcp',
              routerLink: '/admin/acp-rapport',
              icon: 'pi pi-file-export'
            }
          ]
        },
        {
          label: 'nav.system',
          icon: 'pi pi-cog',
          children: [
            {
              label: 'nav.users',
              routerLink: '/admin/useradmin',
              icon: 'pi pi-user'
            },
            {
              label: 'nav.events',
              routerLink: '/admin/eventadmin',
              icon: 'pi pi-calendar'
            },
            {
              label: 'nav.clubs',
              routerLink: '/admin/clubadmin',
              icon: 'pi pi-shield'
            },
            {
              label: 'nav.organizers',
              routerLink: '/admin/organizeradmin',
              icon: 'pi pi-users'
            },
            {
              label: 'nav.checkpoints',
              routerLink: '/admin/siteadmin',
              icon: 'pi pi-map-marker'
            }
          ]
        }
      );
    }

    // Add volunteer menu section for volunteers, superusers, or admins
    // All admin-level users should see volunteer section
    if (isVolunteer || isSuperUser || isAdmin) {
      menuItems.push(
        {
          label: 'nav.volunteer',
          icon: 'pi pi-heart',
          children: [
            {
              label: 'nav.volunteer',
              routerLink: '/volunteer',
              icon: 'pi pi-users'
            }
          ]
        }
      );
    }

    console.log('SidebarService: Final menu items:', menuItems);
    return menuItems;
  }

  public getMenuItemsForCurrentUser(): Observable<SidebarMenuItem[]> {
    return this.menuItems$;
  }

  public shouldShowSidebar(): Observable<boolean> {
    // Check both menu items and authentication status
    return this.menuItems$.pipe(
      map(menuItems => {
        const hasMenuItems = menuItems.length > 0;
        const isAuthenticated = this.authenticatedService.authenticatedSubject.value;
        return hasMenuItems && isAuthenticated;
      })
    );
  }

  public refreshSidebar(): void {
    // Force refresh the sidebar menu items
    console.log('SidebarService: Force refreshing sidebar');
    this.checkUserFromStorage();
  }

  public toggleSidebar(): void {
    const currentState = this.sidebarOpenSubject.value;
    this.sidebarOpenSubject.next(!currentState);
  }

  public openSidebar(): void {
    this.sidebarOpenSubject.next(true);
  }

  public closeSidebar(): void {
    this.sidebarOpenSubject.next(false);
  }
}
