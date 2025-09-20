import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LogoComponent } from '../logo/logo.component';
import { FlagLanguageSelectorComponent } from '../flag-language-selector/flag-language-selector.component';

@Component({
  selector: 'app-competitor-header',
  standalone: true,
  imports: [CommonModule, LogoComponent, FlagLanguageSelectorComponent],
  templateUrl: './competitor-header.component.html',
  styleUrl: './competitor-header.component.scss'
})
export class CompetitorHeaderComponent {
  @Input() startNumber: string = '#123';
  @Input() riderName: string = 'John Andersson';
  @Input() locationStatus: 'granted' | 'denied' | 'unknown' = 'unknown';
  @Output() logout = new EventEmitter<void>();

  onLogout() {
    this.logout.emit();
  }

  getLocationStatusText(): string {
    switch (this.locationStatus) {
      case 'granted':
        return 'Location access granted';
      case 'denied':
        return 'Location access denied';
      case 'unknown':
      default:
        return 'Location status unknown';
    }
  }
}
