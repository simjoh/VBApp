import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { GeolocationService } from '../../core/services/geolocation.service';
import { MessageService } from '../../core/services/message.service';
import { AuthService } from '../../core/services/auth.service';
import { CompetitorHeaderComponent } from '../../shared/components/competitor-header/competitor-header.component';
import { CheckpointCardComponent, CheckpointData } from '../../shared/components/checkpoint-card/checkpoint-card.component';
import { firstValueFrom } from 'rxjs';

@Component({
  selector: 'app-competitor',
  standalone: true,
  imports: [CommonModule, CompetitorHeaderComponent, CheckpointCardComponent],
  templateUrl: './competitor.component.html',
  styleUrl: './competitor.component.scss'
})
export class CompetitorComponent implements OnInit {
  private router = inject(Router);
  private geolocationService = inject(GeolocationService);
  private messageService = inject(MessageService);
  private authService = inject(AuthService);

  hasGeolocationPermission = signal<boolean | null>(null);
  isCheckingPermission = signal(true);

  // Hardcoded checkpoint data
  checkpoints: CheckpointData[] = [
    {
      name: 'BROPARKEN UMEÅ',
      distance: 0,
      toNext: 28,
      opens: '2025-05-10 08:00',
      closes: '2025-05-10 09:00',
      service: 'Start-Målkontroll',
      time: '2025-05-10 08:00:00',
      logoFileName: 'umea_logo.png',
      status: 'checked-in'
    },
    {
      name: 'TAPAS BAR DELI UMEÅ',
      distance: 200,
      toNext: 0,
      opens: '2025-05-10 13:53',
      closes: '2025-05-10 21:30',
      service: 'Mat till försäljning, WC',
      time: '2025-05-10 15:33:23',
      logoFileName: 'tapas_logo.png',
      status: 'checked-in'
    },
    {
      name: 'SKELLEFTEÅ',
      distance: 85,
      toNext: 42,
      opens: '2025-05-10 10:30',
      closes: '2025-05-10 12:00',
      service: 'Checkpoint',
      time: '',
      logoFileName: 'skelleftea_logo.png',
      status: 'open'
    },
    {
      name: 'LYCKSELE',
      distance: 127,
      toNext: 73,
      opens: '2025-05-10 13:00',
      closes: '2025-05-10 15:00',
      service: 'Checkpoint',
      time: '',
      logoFileName: 'lycksele_logo.png',
      status: 'not-visited'
    }
  ];

  ngOnInit() {
    this.checkGeolocationPermission();
  }

  private async checkGeolocationPermission() {
    this.isCheckingPermission.set(true);

    try {
      // Check if geolocation is supported
      if (!this.geolocationService.isSupported$()) {
        this.messageService.showError('Geolocation', 'Geolocation is not supported in your browser');
        this.hasGeolocationPermission.set(false);
        this.isCheckingPermission.set(false);
        return;
      }

      // Check if we have stored permission state
      const permissionGranted = localStorage.getItem('geolocationPermissionGranted');
      const justGranted = localStorage.getItem('geolocationJustGranted');

      if (permissionGranted === 'true') {
        console.log('Permission previously granted, showing dashboard');
        this.hasGeolocationPermission.set(true);

        if (justGranted === 'true') {
          console.log('User just granted permission');
          localStorage.removeItem('geolocationJustGranted');
          this.messageService.showSuccess('Location', 'Location access granted successfully');
        } else {
          console.log('Using previously stored permission');
          this.messageService.showSuccess('Location', 'Location access is enabled');
        }

        this.isCheckingPermission.set(false);
        return;
      }

      if (justGranted === 'true') {
        console.log('User just granted permission, skipping permission check redirect');
        localStorage.removeItem('geolocationJustGranted');
        this.hasGeolocationPermission.set(true);
        this.messageService.showSuccess('Location', 'Location access granted successfully');
        this.isCheckingPermission.set(false);
        return;
      }

      // Check current permission status
      const permission = await navigator.permissions.query({ name: 'geolocation' as PermissionName });
      console.log('Current geolocation permission state:', permission.state);

      if (permission.state === 'granted') {
        this.hasGeolocationPermission.set(true);
        console.log('Permission already granted, showing dashboard');

        // Try to get current position to verify it actually works
        try {
          const position = await firstValueFrom(this.geolocationService.getCurrentPosition());
          console.log('Location verified successfully:', position);
          this.messageService.showSuccess('Location', 'Location access is enabled and working');
        } catch (error) {
          console.warn('Failed to get current position despite permission granted:', error);
          // Still show dashboard but with warning
          this.messageService.showWarning('Location', 'Permission granted but unable to get location');
        }
      } else if (permission.state === 'denied') {
        this.hasGeolocationPermission.set(false);
        console.log('Permission denied, redirecting to permission page');
        this.messageService.showError('Location Required', 'Location access was denied');
        // Only redirect if not coming from permission page
        setTimeout(() => {
          this.router.navigate(['/geolocation-permission']);
        }, 100);
      } else {
        // Permission is 'prompt' - first time or reset
        console.log('Permission is prompt (first time or reset), redirecting to permission page');
        console.log('Current URL:', window.location.href);
        // Only redirect if not already on permission page
        if (!window.location.pathname.includes('geolocation-permission')) {
          setTimeout(() => {
            this.router.navigate(['/geolocation-permission']);
          }, 100);
        }
      }
    } catch (error) {
      console.error('Error checking geolocation permission:', error);
      this.hasGeolocationPermission.set(false);
      this.messageService.showError('Location', 'Failed to check location permission');
      this.router.navigate(['/geolocation-permission']);
    } finally {
      this.isCheckingPermission.set(false);
    }
  }

  requestLocationPermission() {
    this.router.navigate(['/geolocation-permission']);
  }

  logout() {
    console.log('Logging out...');
    this.authService.logout();
    this.router.navigate(['/login']);
  }

  abandonBrevet() {
    console.log('Abandoning brevet...');
    // TODO: Implement abandon brevet logic
    this.messageService.showWarning('Abandon Brevet', 'Brevet abandonment functionality will be implemented soon');
  }

}
