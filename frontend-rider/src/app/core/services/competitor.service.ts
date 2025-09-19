import { Injectable, signal, computed, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, BehaviorSubject, map, tap, catchError, of } from 'rxjs';
import { MessageService } from './message.service';

export interface Checkpoint {
  id: string;
  name: string;
  latitude: number;
  longitude: number;
  radius: number; // in meters
  order: number;
  isRequired: boolean;
  description?: string;
}

export interface Track {
  id: string;
  name: string;
  description?: string;
  distance: number; // in kilometers
  checkpoints: Checkpoint[];
  startTime?: Date;
  endTime?: Date;
}

export interface CompetitorInfo {
  id: string;
  startNumber: string;
  trackId: string;
  name: string;
  email: string;
  phone?: string;
}

export interface CheckpointState {
  checkpointId: string;
  isStamped: boolean;
  isDNF: boolean;
  isCheckedOut: boolean;
  stampedAt?: Date;
  dnfAt?: Date;
  checkedOutAt?: Date;
}

@Injectable({
  providedIn: 'root'
})
export class CompetitorService {
  private httpClient = inject(HttpClient);
  private messageService = inject(MessageService);

  // Track and checkpoint data (static)
  private _currentTrack = signal<Track | null>(null);
  private _checkpoints = signal<Checkpoint[]>([]);
  private _competitorInfo = signal<CompetitorInfo | null>(null);

  // State management (dynamic)
  private _stampStatus = signal<Map<string, boolean>>(new Map());
  private _dnfStatus = signal<Map<string, boolean>>(new Map());
  private _checkoutStatus = signal<Map<string, boolean>>(new Map());
  private _stampTimestamps = signal<Map<string, Date>>(new Map());

  // Read-only signals
  readonly currentTrack$ = this._currentTrack.asReadonly();
  readonly checkpoints$ = this._checkpoints.asReadonly();
  readonly competitorInfo$ = this._competitorInfo.asReadonly();
  readonly stampStatus$ = this._stampStatus.asReadonly();
  readonly dnfStatus$ = this._dnfStatus.asReadonly();
  readonly checkoutStatus$ = this._checkoutStatus.asReadonly();

  // Computed signals
  readonly checkpointStates = computed(() => {
    const checkpoints = this._checkpoints();
    const stampStatus = this._stampStatus();
    const dnfStatus = this._dnfStatus();
    const checkoutStatus = this._checkoutStatus();
    const stampTimestamps = this._stampTimestamps();

    return checkpoints.map(checkpoint => ({
      checkpointId: checkpoint.id,
      isStamped: stampStatus.get(checkpoint.id) || false,
      isDNF: dnfStatus.get(checkpoint.id) || false,
      isCheckedOut: checkoutStatus.get(checkpoint.id) || false,
      stampedAt: stampTimestamps.get(checkpoint.id),
      dnfAt: undefined, // Add if needed
      checkedOutAt: undefined // Add if needed
    }));
  });

  readonly progress = computed(() => {
    const checkpoints = this._checkpoints();
    const stampStatus = this._stampStatus();
    const stampedCount = checkpoints.filter(cp => stampStatus.get(cp.id)).length;
    return {
      total: checkpoints.length,
      stamped: stampedCount,
      percentage: checkpoints.length > 0 ? (stampedCount / checkpoints.length) * 100 : 0
    };
  });

  // Track management
  async loadTrack(trackId: string, startNumber: string): Promise<void> {
    try {
      const track = await this.httpClient.get<Track>(`/api/randonneur/track-only/${trackId}/startnumber/${startNumber}`).toPromise();
      if (track) {
        this._currentTrack.set(track);
        this._checkpoints.set(track.checkpoints);
        this.messageService.showSuccess('Track Loaded', `Loaded track: ${track.name}`);
      }
    } catch (error) {
      console.error('Error loading track:', error);
      this.messageService.showError('Error', 'Failed to load track');
    }
  }

  // Competitor info
  async loadCompetitorInfo(competitorId: string): Promise<void> {
    try {
      const competitor = await this.httpClient.get<CompetitorInfo>(`/api/competitor/${competitorId}`).toPromise();
      if (competitor) {
        this._competitorInfo.set(competitor);
        this.messageService.showSuccess('Competitor Info', `Loaded info for ${competitor.name}`);
      }
    } catch (error) {
      console.error('Error loading competitor info:', error);
      this.messageService.showError('Error', 'Failed to load competitor info');
    }
  }

  // State management
  async loadCheckpointStates(competitorId: string, trackId: string): Promise<void> {
    try {
      const states = await this.httpClient.get<CheckpointState[]>(`/api/competitor/${competitorId}/track/${trackId}/states`).toPromise();
      if (states) {
        const stampStatus = new Map<string, boolean>();
        const dnfStatus = new Map<string, boolean>();
        const checkoutStatus = new Map<string, boolean>();
        const stampTimestamps = new Map<string, Date>();

        states.forEach(state => {
          stampStatus.set(state.checkpointId, state.isStamped);
          dnfStatus.set(state.checkpointId, state.isDNF);
          checkoutStatus.set(state.checkpointId, state.isCheckedOut);
          if (state.stampedAt) {
            stampTimestamps.set(state.checkpointId, new Date(state.stampedAt));
          }
        });

        this._stampStatus.set(stampStatus);
        this._dnfStatus.set(dnfStatus);
        this._checkoutStatus.set(checkoutStatus);
        this._stampTimestamps.set(stampTimestamps);
      }
    } catch (error) {
      console.error('Error loading checkpoint states:', error);
      this.messageService.showError('Error', 'Failed to load checkpoint states');
    }
  }

  // Stamp management
  async stampCheckpoint(checkpointId: string, latitude: number, longitude: number): Promise<boolean> {
    try {
      const competitor = this._competitorInfo();
      if (!competitor) {
        this.messageService.showError('Error', 'No competitor info available');
        return false;
      }

      const response = await this.httpClient.post<{ success: boolean; timestamp: string }>(
        `/api/competitor/${competitor.id}/checkpoint/${checkpointId}/stamp`,
        { latitude, longitude }
      ).toPromise();

      if (response?.success) {
        this._stampStatus.update(status => {
          const newStatus = new Map(status);
          newStatus.set(checkpointId, true);
          return newStatus;
        });
        this._stampTimestamps.update(timestamps => {
          const newTimestamps = new Map(timestamps);
          newTimestamps.set(checkpointId, new Date(response.timestamp));
          return newTimestamps;
        });
        this.messageService.showSuccess('Checkpoint Stamped', 'Successfully stamped checkpoint');
        return true;
      }
      return false;
    } catch (error) {
      console.error('Error stamping checkpoint:', error);
      this.messageService.showError('Error', 'Failed to stamp checkpoint');
      return false;
    }
  }

  async rollbackStamp(checkpointId: string): Promise<boolean> {
    try {
      const competitor = this._competitorInfo();
      if (!competitor) {
        this.messageService.showError('Error', 'No competitor info available');
        return false;
      }

      const response = await this.httpClient.put<{ success: boolean }>(
        `/api/competitor/${competitor.id}/checkpoint/${checkpointId}/rollback`,
        {}
      ).toPromise();

      if (response?.success) {
        this._stampStatus.update(status => {
          const newStatus = new Map(status);
          newStatus.set(checkpointId, false);
          return newStatus;
        });
        this._stampTimestamps.update(timestamps => {
          const newTimestamps = new Map(timestamps);
          newTimestamps.delete(checkpointId);
          return newTimestamps;
        });
        this.messageService.showSuccess('Rollback Successful', 'Checkpoint stamp rolled back');
        return true;
      }
      return false;
    } catch (error) {
      console.error('Error rolling back stamp:', error);
      this.messageService.showError('Error', 'Failed to rollback stamp');
      return false;
    }
  }

  // DNF management
  async setDNF(checkpointId: string, dnf: boolean): Promise<boolean> {
    try {
      const competitor = this._competitorInfo();
      if (!competitor) {
        this.messageService.showError('Error', 'No competitor info available');
        return false;
      }

      const endpoint = dnf ? 'dnf' : 'dnf/rollback';
      const response = await this.httpClient.put<{ success: boolean }>(
        `/api/competitor/${competitor.id}/checkpoint/${checkpointId}/${endpoint}`,
        {}
      ).toPromise();

      if (response?.success) {
        this._dnfStatus.update(status => {
          const newStatus = new Map(status);
          newStatus.set(checkpointId, dnf);
          return newStatus;
        });
        const message = dnf ? 'Marked as DNF' : 'DNF status removed';
        this.messageService.showSuccess('DNF Status', message);
        return true;
      }
      return false;
    } catch (error) {
      console.error('Error setting DNF status:', error);
      this.messageService.showError('Error', 'Failed to update DNF status');
      return false;
    }
  }

  // Checkout management
  async setCheckout(checkpointId: string, checkedOut: boolean): Promise<boolean> {
    try {
      const competitor = this._competitorInfo();
      if (!competitor) {
        this.messageService.showError('Error', 'No competitor info available');
        return false;
      }

      const endpoint = checkedOut ? 'checkout' : 'checkout/rollback';
      const response = await this.httpClient.put<{ success: boolean }>(
        `/api/competitor/${competitor.id}/checkpoint/${checkpointId}/${endpoint}`,
        {}
      ).toPromise();

      if (response?.success) {
        this._checkoutStatus.update(status => {
          const newStatus = new Map(status);
          newStatus.set(checkpointId, checkedOut);
          return newStatus;
        });
        const message = checkedOut ? 'Checked out' : 'Checkout rolled back';
        this.messageService.showSuccess('Checkout Status', message);
        return true;
      }
      return false;
    } catch (error) {
      console.error('Error setting checkout status:', error);
      this.messageService.showError('Error', 'Failed to update checkout status');
      return false;
    }
  }

  // Utility methods
  isStamped(checkpointId: string): boolean {
    return this._stampStatus().get(checkpointId) || false;
  }

  isDNF(checkpointId: string): boolean {
    return this._dnfStatus().get(checkpointId) || false;
  }

  isCheckedOut(checkpointId: string): boolean {
    return this._checkoutStatus().get(checkpointId) || false;
  }

  getStampedAt(checkpointId: string): Date | undefined {
    return this._stampTimestamps().get(checkpointId);
  }

  // Clear all data
  clearData(): void {
    this._currentTrack.set(null);
    this._checkpoints.set([]);
    this._competitorInfo.set(null);
    this._stampStatus.set(new Map());
    this._dnfStatus.set(new Map());
    this._checkoutStatus.set(new Map());
    this._stampTimestamps.set(new Map());
  }
}
