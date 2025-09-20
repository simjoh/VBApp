import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { GeolocationService } from '../../core/services/geolocation.service';
import { MessageService } from '../../core/services/message.service';
import { Router, NavigationEnd } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { filter } from 'rxjs/operators';

@Component({
  selector: 'app-geolocation-permission',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './geolocation-permission.component.html',
  styleUrl: './geolocation-permission.component.scss'
})
export class GeolocationPermissionComponent {
  private geolocationService = inject(GeolocationService);
  private messageService = inject(MessageService);
  private router = inject(Router);

  isRequesting = signal(false);
  permissionGranted = signal(false);
  isDetectingLocation = signal(false);
  error = signal<string | null>(null);

  constructor() {
    // Listen for navigation events to debug
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe(event => {
      console.log('Navigation event:', event);
      console.log('Navigation URL:', event.url);
      console.log('Current pathname:', window.location.pathname);
    });
  }

  async requestPermission() {
    this.isRequesting.set(true);
    this.error.set(null);

    try {
      // Check if geolocation is supported
      if (!this.geolocationService.isSupported$()) {
        this.error.set('Geolocation is not supported in your browser');
        this.isRequesting.set(false);
        return;
      }

      // Request permission
      const granted = await firstValueFrom(this.geolocationService.requestPermission());

      if (granted) {
        this.permissionGranted.set(true);
        this.messageService.showSuccess('Geolocation Permission', 'Location access granted!');

        // Get initial position to verify it works
        this.isDetectingLocation.set(true);
        try {
          const position = await firstValueFrom(this.geolocationService.getCurrentPosition());
          console.log('Location detected:', position);
          this.messageService.showInfo('Location', 'Your location has been detected successfully');

          // Navigate to competitor dashboard immediately after successful location detection
          console.log('Redirecting to competitor dashboard...');
          // Set permanent flag to indicate permission was granted
          localStorage.setItem('geolocationJustGranted', 'true');
          localStorage.setItem('geolocationPermissionGranted', 'true');
          // Use direct window.location for immediate redirect
          this.router.navigateByUrl('/dashboard', { replaceUrl: true });
        } catch (posError) {
          console.error('Failed to get initial position:', posError);
          this.messageService.showWarning('Location', 'Permission granted but unable to get your current location');
          // Still redirect even if position detection fails
          console.log('Redirecting despite location detection failure...');
          setTimeout(() => {
            localStorage.setItem('geolocationJustGranted', 'true');
            localStorage.setItem('geolocationPermissionGranted', 'true');
            this.router.navigateByUrl('/dashboard');

          }, 2000);
        } finally {
          this.isDetectingLocation.set(false);
        }
      } else {
        this.error.set('Location access was denied. This app requires location access to function properly.');
        this.messageService.showError('Geolocation Permission', 'Location access is required for this app');
      }
    } catch (error) {
      console.error('Permission request error:', error);
      this.error.set('Failed to request location permission. Please try again.');
      this.messageService.showError('Geolocation Permission', 'Failed to request location access');
    } finally {
      this.isRequesting.set(false);
    }
  }

  retryPermission() {
    this.error.set(null);
    this.requestPermission();
  }

  private redirectToDashboard() {
    console.log('Attempting redirect to dashboard...');
    console.log('Current URL before redirect:', window.location.href);

    // Add a small delay to ensure the UI updates
    setTimeout(() => {
      // Try router navigation first
      this.router.navigateByUrl('/', { replaceUrl: true }).then(success => {
        console.log('navigateByUrl successful:', success);
        console.log('URL after navigateByUrl:', window.location.href);

        // Check if we're actually on the right page
        setTimeout(() => {
          console.log('URL after 500ms:', window.location.href);
          if (window.location.pathname === '/geolocation-permission') {
            console.log('Still on permission page, forcing redirect...');
            window.location.href = '/dashboard';
          }
        }, 500);

        if (!success) {
          console.log('navigateByUrl failed, trying navigate...');
          this.router.navigate(['/'], { replaceUrl: true }).then(navSuccess => {
            console.log('navigate successful:', navSuccess);
            if (!navSuccess) {
              console.log('Router navigation failed, using window.location...');
              window.location.href = '/dashboard';
            }
          }).catch(navError => {
            console.error('navigate failed:', navError);
            console.log('Router navigation failed, using window.location...');
            window.location.href = '/dashboard';
          });
        }
      }).catch(error => {
        console.error('navigateByUrl failed:', error);
        console.log('Router navigation failed, using window.location...');
        window.location.href = '/dashboard';
      });
    }, 100);
  }


}
