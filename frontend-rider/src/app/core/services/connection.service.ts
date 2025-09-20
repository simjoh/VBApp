import { Injectable, signal } from '@angular/core';

export interface ConnectionInfo {
  isOnline: boolean;
  effectiveType: string;
  downlink: number;
  rtt: number;
  saveData: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class ConnectionService {
  private _isOnline = signal(navigator.onLine);
  private _connectionInfo = signal<ConnectionInfo>(this.getConnectionInfo());
  private _isSlowConnection = signal(false);
  private slowConnectionTimeout?: number;

  readonly isOnline = this._isOnline.asReadonly();
  readonly connectionInfo = this._connectionInfo.asReadonly();
  readonly isSlowConnection = this._isSlowConnection.asReadonly();

  constructor() {
    this.initializeConnectionMonitoring();
    this.updateConnectionStatus();
  }

  private initializeConnectionMonitoring(): void {
    // Listen for online/offline events
    window.addEventListener('online', () => {
      this._isOnline.set(true);
      this.updateConnectionStatus();
    });

    window.addEventListener('offline', () => {
      this._isOnline.set(false);
      this.updateConnectionStatus();
    });

    // Listen for connection changes (if supported)
    if ('connection' in navigator) {
      const connection = (navigator as any).connection;
      connection?.addEventListener('change', () => {
        this.updateConnectionStatus();
      });
    }

    // Periodic connection check
    setInterval(() => {
      this.updateConnectionStatus();
    }, 30000); // Check every 30 seconds
  }

  private updateConnectionStatus(): void {
    const info = this.getConnectionInfo();
    this._connectionInfo.set(info);

    // Clear any existing timeout
    if (this.slowConnectionTimeout) {
      clearTimeout(this.slowConnectionTimeout);
    }

    const isSlowDetected = this.detectSlowConnection(info);

    if (isSlowDetected) {
      // Wait 2 seconds before marking as slow to avoid false positives
      this.slowConnectionTimeout = window.setTimeout(() => {
        this._isSlowConnection.set(true);
      }, 2000);
    } else {
      this._isSlowConnection.set(false);
    }
  }

  private getConnectionInfo(): ConnectionInfo {
    const defaultInfo: ConnectionInfo = {
      isOnline: navigator.onLine,
      effectiveType: 'unknown',
      downlink: 0,
      rtt: 0,
      saveData: false
    };

    if ('connection' in navigator) {
      const connection = (navigator as any).connection;
      return {
        isOnline: navigator.onLine,
        effectiveType: connection?.effectiveType || 'unknown',
        downlink: connection?.downlink || 0,
        rtt: connection?.rtt || 0,
        saveData: connection?.saveData || false
      };
    }

    return defaultInfo;
  }

  private detectSlowConnection(info: ConnectionInfo): boolean {
    if (!info.isOnline) return false;

    // If connection API is not supported or returns no data, assume it's not slow
    if (info.effectiveType === 'unknown' && info.downlink === 0 && info.rtt === 0) {
      return false;
    }

    // Consider connection slow if:
    // - Effective type is slow-2g or 2g
    // - RTT is very high (> 1000ms) AND we have valid RTT data
    // - Downlink is very low (< 0.5 Mbps) AND we have valid downlink data
    const slowTypes = ['slow-2g', '2g'];
    const isSlowType = slowTypes.includes(info.effectiveType);
    const isHighRTT = info.rtt > 0 && info.rtt > 1000; // Only check RTT if we have data
    const isLowDownlink = info.downlink > 0 && info.downlink < 0.5; // Only check downlink if we have data

    console.log('🔍 Connection detection:', {
      effectiveType: info.effectiveType,
      downlink: info.downlink,
      rtt: info.rtt,
      isSlowType,
      isHighRTT,
      isLowDownlink,
      result: isSlowType || isHighRTT || isLowDownlink
    });

    return isSlowType || isHighRTT || isLowDownlink;
  }

  /**
   * Get a user-friendly connection description
   */
  getConnectionDescription(): string {
    const info = this._connectionInfo();

    if (!info.isOnline) {
      return 'Offline';
    }

    switch (info.effectiveType) {
      case 'slow-2g':
        return 'Very slow connection';
      case '2g':
        return 'Slow connection';
      case '3g':
        return 'Moderate connection';
      case '4g':
        return 'Fast connection';
      default:
        if (this._isSlowConnection()) {
          return 'Slow connection detected';
        }
        return 'Connected';
    }
  }

  /**
   * Check if we should show loading indicators for slow connections
   */
  shouldShowSlowConnectionWarning(): boolean {
    return this._isSlowConnection() && this._isOnline();
  }
}
