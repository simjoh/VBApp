import { Component, Input, Output, EventEmitter, signal, ViewChild, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Popover, PopoverModule } from 'primeng/popover';
import { LogoComponent } from '../logo/logo.component';
import { FlagLanguageSelectorComponent } from '../flag-language-selector/flag-language-selector.component';
import { TranslationPipe } from '../../pipes/translation.pipe';
import { TranslationService } from '../../../core/services/translation.service';

@Component({
  selector: 'app-competitor-header',
  standalone: true,
  imports: [CommonModule, PopoverModule, LogoComponent, FlagLanguageSelectorComponent, TranslationPipe],
  templateUrl: './competitor-header.component.html',
  styleUrl: './competitor-header.component.scss'
})
export class CompetitorHeaderComponent {
  @Input() startNumber: string = '#123';
  @Input() riderName: string = 'John Andersson';
  @Input() locationStatus: 'granted' | 'denied' | 'unknown' = 'unknown';
  @Input() currentCoordinates: { latitude: number; longitude: number } | null = null;
  @Input() isLocationFresh: boolean = false;
  @Output() logout = new EventEmitter<void>();

  @ViewChild('locationPopover') locationPopover!: Popover;

  private translationService = inject(TranslationService);
  isUpdating = signal(false);

  onLogout() {
    this.logout.emit();
  }

  toggleLocationPopover(event: Event) {
    this.locationPopover.toggle(event);
  }

  /**
   * Trigger rotation animation for location updates
   */
  triggerLocationUpdate() {
    this.isUpdating.set(true);
    // Reset animation after completion
    setTimeout(() => this.isUpdating.set(false), 1000);
  }

  getLocationStatusText(): string {
    let baseText = '';

    switch (this.locationStatus) {
      case 'granted':
        baseText = 'Location access granted';
        break;
      case 'denied':
        baseText = 'Location access denied';
        break;
      case 'unknown':
      default:
        baseText = 'Location status unknown';
        break;
    }

    // Add coordinates and freshness info if available
    if (this.currentCoordinates && this.locationStatus === 'granted') {
      const lat = this.currentCoordinates.latitude.toFixed(6);
      const lng = this.currentCoordinates.longitude.toFixed(6);
      baseText += `\nCurrent position: ${lat}, ${lng}`;

      if (this.isLocationFresh) {
        baseText += '\n🟢 Location is fresh (≤1 min old)';
      } else {
        baseText += '\n🟡 Location may be outdated (>1 min old)';
      }
    }

    return baseText;
  }

  getLocationStatusLabel(): string {
    const translate = this.translationService.translate.bind(this.translationService);
    switch (this.locationStatus) {
      case 'granted':
        return translate('geolocation.statusGranted');
      case 'denied':
        return translate('geolocation.statusDenied');
      case 'unknown':
      default:
        return translate('geolocation.statusUnknown');
    }
  }
}
