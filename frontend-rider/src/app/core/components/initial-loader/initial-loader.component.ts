import { Component, inject, signal, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, NavigationEnd } from '@angular/router';
import { ConnectionService } from '../../services/connection.service';
import { LogoComponent } from '../../../shared/components/logo/logo.component';
import { filter } from 'rxjs/operators';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-initial-loader',
  standalone: true,
  imports: [CommonModule, LogoComponent],
  template: `
    @if (isLoading()) {
      <div class="initial-loader" [class.slow-connection]="connectionService.shouldShowSlowConnectionWarning()">
        <div class="loader-content">
          <div class="logo-container">
            <app-logo [height]="120" [width]="120" [rolling]="true"></app-logo>
          </div>

          <div class="loading-info">
            <h2>Riders App</h2>
            <p class="loading-text">{{ getLoadingText() }}</p>

            @if (connectionService.shouldShowSlowConnectionWarning()) {
              <div class="slow-connection-notice">
                <i class="pi pi-exclamation-triangle"></i>
                <span>{{ connectionService.getConnectionDescription() }}</span>
              </div>
            }
          </div>

          <div class="loading-bar">
            <div class="loading-progress" [style.width.%]="loadingProgress()"></div>
          </div>

          <div class="loading-dots">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
    }
  `,
  styles: [`
    .initial-loader {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      animation: fadeIn 0.3s ease-out;

      &.slow-connection {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      }
    }

    .loader-content {
      text-align: center;
      color: white;
      max-width: 400px;
      padding: 2rem;
    }

    .logo-container {
      margin-bottom: 2rem;
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    .loading-info {
      margin-bottom: 2rem;

      h2 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
      }

      .loading-text {
        font-size: 1.1rem;
        margin: 0;
        opacity: 0.9;
        font-weight: 500;
      }
    }

    .slow-connection-notice {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 1rem;
      padding: 0.75rem 1rem;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      backdrop-filter: blur(10px);
      font-size: 0.9rem;
      font-weight: 500;

      i {
        font-size: 1rem;
      }
    }

    .loading-bar {
      width: 100%;
      height: 4px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 2px;
      overflow: hidden;
      margin-bottom: 1.5rem;
    }

    .loading-progress {
      height: 100%;
      background: rgba(255, 255, 255, 0.8);
      border-radius: 2px;
      transition: width 0.3s ease;
      animation: shimmer 1.5s infinite;
    }

    .loading-dots {
      display: flex;
      justify-content: center;
      gap: 0.5rem;

      span {
        width: 8px;
        height: 8px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        animation: bounce 1.4s infinite ease-in-out both;

        &:nth-child(1) { animation-delay: -0.32s; }
        &:nth-child(2) { animation-delay: -0.16s; }
        &:nth-child(3) { animation-delay: 0s; }
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes shimmer {
      0% {
        background-position: -200px 0;
      }
      100% {
        background-position: calc(200px + 100%) 0;
      }
    }

    @keyframes bounce {
      0%, 80%, 100% {
        transform: scale(0.8);
      }
      40% {
        transform: scale(1);
      }
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
      .loader-content {
        padding: 1rem;
        max-width: 300px;

        h2 {
          font-size: 1.5rem;
        }

        .loading-text {
          font-size: 1rem;
        }
      }

      .logo-container {
        margin-bottom: 1.5rem;
      }
    }
  `]
})
export class InitialLoaderComponent implements OnInit, OnDestroy {
  private router = inject(Router);
  connectionService = inject(ConnectionService);

  // Set to false to completely disable the initial loader
  private readonly ENABLE_LOADER = false;

  isLoading = signal(this.ENABLE_LOADER);
  loadingProgress = signal(0);

  private routerSubscription?: Subscription;
  private progressInterval?: number;

  ngOnInit() {
    if (!this.ENABLE_LOADER) {
      this.isLoading.set(false);
      return;
    }

    this.startProgressAnimation();
    this.listenForRouteChanges();

    // For fast connections, hide loader quickly after app initialization
    if (!this.connectionService.shouldShowSlowConnectionWarning()) {
      // Very quick for fast connections
      setTimeout(() => {
        this.hideLoader();
      }, 500);
    } else {
      // For slow connections, give more time
      setTimeout(() => {
        this.hideLoader();
      }, 2500);
    }
  }

  ngOnDestroy() {
    this.routerSubscription?.unsubscribe();
    if (this.progressInterval) {
      clearInterval(this.progressInterval);
    }
  }

  private startProgressAnimation() {
    this.progressInterval = window.setInterval(() => {
      const current = this.loadingProgress();
      if (current < 90) {
        // Slower progress for slow connections, faster for normal connections
        const increment = this.connectionService.shouldShowSlowConnectionWarning() ? 2 : 8;
        const interval = this.connectionService.shouldShowSlowConnectionWarning() ? 150 : 80;
        this.loadingProgress.set(Math.min(current + increment, 90));
      }
    }, this.connectionService.shouldShowSlowConnectionWarning() ? 150 : 80);
  }

  private listenForRouteChanges() {
    this.routerSubscription = this.router.events
      .pipe(filter(event => event instanceof NavigationEnd))
      .subscribe(() => {
        this.completeLoading();
      });
  }

  private completeLoading() {
    this.loadingProgress.set(100);
    setTimeout(() => {
      this.hideLoader();
    }, 300);
  }

  private hideLoader() {
    this.isLoading.set(false);
    if (this.progressInterval) {
      clearInterval(this.progressInterval);
    }
  }

  getLoadingText(): string {
    if (!this.connectionService.isOnline()) {
      return 'Connecting...';
    }

    if (this.connectionService.shouldShowSlowConnectionWarning()) {
      return 'Loading (slow connection)...';
    }

    const progress = this.loadingProgress();
    if (progress < 30) {
      return 'Initializing...';
    } else if (progress < 60) {
      return 'Loading app...';
    } else if (progress < 90) {
      return 'Almost ready...';
    } else {
      return 'Finishing up...';
    }
  }
}
