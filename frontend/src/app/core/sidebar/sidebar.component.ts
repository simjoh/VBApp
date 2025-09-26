import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { SidebarService, SidebarMenuItem } from './sidebar.service';
import { AuthService } from '../auth/auth.service';

@Component({
  selector: 'brevet-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss'],
  changeDetection: ChangeDetectionStrategy.Default
})
export class SidebarComponent implements OnInit {
  menuItems$: Observable<SidebarMenuItem[]>;
  isAdminUser$: Observable<boolean>;
  sidebarOpen$: Observable<boolean>;

  constructor(
    private sidebarService: SidebarService,
    private authService: AuthService
  ) {
    // Get menu items immediately and also as observable for updates
    this.menuItems$ = this.sidebarService.getMenuItemsForCurrentUser();
    this.sidebarOpen$ = this.sidebarService.sidebarOpen$;
    this.isAdminUser$ = this.authService.$auth$.pipe(
      map(user => {
        if (!user) return false;
        return user.roles.includes('ADMIN') || user.roles.includes('SUPERUSER');
      })
    );
  }

  ngOnInit(): void {}

  logout(): void {
    this.authService.logoutUser();
  }

  hasChildren(item: SidebarMenuItem): boolean {
    return !!(item.children && item.children.length > 0);
  }

  toggleSidebar(): void {
    this.sidebarService.toggleSidebar();
  }

  closeSidebar(): void {
    this.sidebarService.closeSidebar();
  }
}
