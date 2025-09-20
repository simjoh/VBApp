import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ConnectionService } from '../../services/connection.service';
import { TranslationPipe } from '../../../shared/pipes/translation.pipe';

@Component({
  selector: 'app-connection-status',
  standalone: true,
  imports: [CommonModule, TranslationPipe],
  template: `
    @if (!connectionService.isOnline()) {
      <div class="connection-banner offline">
        <div class="banner-content">
          <i class="pi pi-wifi" style="opacity: 0.5;"></i>
          <span class="banner-text">{{ 'connection.offline' | translate }}</span>
        </div>
      </div>
    } @else if (shouldShowSlowBanner()) {
      <div class="connection-banner slow">
        <div class="banner-content">
          <div class="slow-indicator">
            <i class="pi pi-clock"></i>
          </div>
          <span class="banner-text">{{ 'connection.slowConnection' | translate }}</span>
        </div>
      </div>
    }
  `,
  styles: [`
    .connection-banner {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 9999;
      padding: 0.75rem 1rem;
      display: flex;
      justify-content: center;
      animation: slideDown 0.3s ease-out;

      &.offline {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white;
      }

      &.slow {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
      }
    }

    .banner-content {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.9rem;
      font-weight: 500;
      text-align: center;
    }

    .slow-indicator {
      animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes slideDown {
      from {
        transform: translateY(-100%);
      }
      to {
        transform: translateY(0);
      }
    }

    @keyframes pulse {
      0%, 100% {
        opacity: 1;
      }
      50% {
        opacity: 0.5;
      }
    }

    /* Adjust body padding when banner is shown */
    :host {
      & + * {
        padding-top: 60px;
      }
    }
  `]
})
export class ConnectionStatusComponent {
  connectionService = inject(ConnectionService);

  // Temporarily disable to avoid false positives
  private readonly ENABLE_SLOW_CONNECTION_BANNER = false;

  shouldShowSlowBanner(): boolean {
    return this.ENABLE_SLOW_CONNECTION_BANNER && this.connectionService.shouldShowSlowConnectionWarning();
  }
}
