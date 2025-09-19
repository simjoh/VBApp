import { Component, Input, Output, EventEmitter, inject, signal, computed, OnChanges, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { MessageService } from '../../../core/services/message.service';
import { environment } from '../../../../environments/environment';
import { firstValueFrom } from 'rxjs';

export interface CheckpointButtonData {
  checkpoint_uid: string;
  name: string;
  status: 'checked-in' | 'checked-out' | 'not-visited';
  timestamp?: string;
  checkoutTimestamp?: string;
  isFirst: boolean;
  isLast: boolean;
}

export interface ButtonActionResponse {
  success: boolean;
  message?: string;
  newState?: any;
}

@Component({
  selector: 'app-checkpoint-button',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './checkpoint-button.component.html',
  styleUrl: './checkpoint-button.component.scss'
})
export class CheckpointButtonComponent implements OnChanges {
  @Input({ required: true }) checkpoint!: CheckpointButtonData;
  @Input({ required: true }) trackUid!: string;
  @Input({ required: true }) participantUid!: string;
  @Input({ required: true }) startNumber!: string;
  @Input() disabled: boolean = false;

  @Output() actionCompleted = new EventEmitter<{
    action: string;
    checkpoint: CheckpointButtonData;
    response: ButtonActionResponse;
  }>();

  private http = inject(HttpClient);
  private messageService = inject(MessageService);

  // Loading state
  private isLoading = signal(false);

  // Reactive signals for input properties
  private checkpointSignal = signal<CheckpointButtonData | null>(null);

  // Track which action to show for checked-in state
  private currentActionIndex = signal(0);

  // Computed button state based on checkpoint status and position
  private buttonState = computed(() => {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) return 'check-in';

    // First checkpoint shows actual status but always triggers start action
    if (checkpoint.isFirst) {
      return 'start';
    }

    // Last checkpoint shows "FINISHED" when checked in or checked out
    if (checkpoint.isLast && (checkpoint.status === 'checked-in' || checkpoint.status === 'checked-out')) {
      return 'finish';
    }

    // Regular checkpoints follow normal flow
    switch (checkpoint.status) {
      case 'not-visited':
        return 'check-in';
      case 'checked-in':
        // For checked-in state, cycle between check-out and undo-checkin
        const actions = ['check-out', 'undo-checkin'];
        return actions[this.currentActionIndex() % actions.length];
      case 'checked-out':
        return 'undo-checkout';
      default:
        return 'check-in';
    }
  });

  ngOnChanges(changes: SimpleChanges) {
    if (changes['checkpoint'] && changes['checkpoint'].currentValue) {
      this.checkpointSignal.set(changes['checkpoint'].currentValue);
    }
  }

  /**
   * Get button text based on current state
   */
  get buttonText(): string {
    const state = this.buttonState();
    const checkpoint = this.checkpointSignal();

    switch (state) {
      case 'start':
        // First checkpoint shows actual status
        if (checkpoint?.status === 'not-visited') {
          return 'CHECK IN';
        } else if (checkpoint?.status === 'checked-in') {
          return 'CHECKED IN';
        } else if (checkpoint?.status === 'checked-out') {
          return 'CHECKED IN';
        }
        return 'CHECK IN';
      case 'finish':
        return 'FINISHED';
      case 'check-in':
        return 'CHECK IN';
      case 'check-out':
        return 'CHECK OUT';
      case 'undo-checkout':
        return 'UNDO CHECK OUT';
      case 'undo-checkin':
        return 'UNDO CHECK IN';
      default:
        return 'CHECK IN';
    }
  }


  /**
   * Get button icon based on current state
   */
  get buttonIcon(): string {
    const state = this.buttonState();
    const checkpoint = this.checkpointSignal();

    switch (state) {
      case 'start':
        // First checkpoint shows icon based on actual status
        if (checkpoint?.status === 'not-visited') {
          return 'pi-sign-in';
        } else if (checkpoint?.status === 'checked-in') {
          return 'pi-check';
        } else if (checkpoint?.status === 'checked-out') {
          return 'pi-sign-out';
        }
        return 'pi-sign-in';
      case 'finish':
        return 'pi-check-circle';
      case 'check-in':
        return 'pi-sign-in';
      case 'check-out':
        return 'pi-sign-out';
      case 'undo-checkout':
        return 'pi-undo';
      case 'undo-checkin':
        return 'pi-undo';
      default:
        return 'pi-circle';
    }
  }

  /**
   * Get button CSS class based on current state
   */
  get buttonClass(): string {
    const state = this.buttonState();
    const checkpoint = this.checkpointSignal();
    const baseClass = 'checkpoint-button';

    switch (state) {
      case 'start':
        // First checkpoint uses styling based on actual status
        if (checkpoint?.status === 'not-visited') {
          return `${baseClass} start check-in`;
        } else if (checkpoint?.status === 'checked-in') {
          return `${baseClass} start checked-in`;
        } else if (checkpoint?.status === 'checked-out') {
          return `${baseClass} start checked-out`;
        }
        return `${baseClass} start check-in`;
      case 'finish':
        return `${baseClass} finish`;
      case 'check-in':
        return `${baseClass} check-in`;
      case 'check-out':
        return `${baseClass} check-out`;
      case 'undo-checkout':
        return `${baseClass} undo`;
      case 'undo-checkin':
        return `${baseClass} undo`;
      default:
        return baseClass;
    }
  }

  /**
   * Check if button should be disabled
   */
  get isButtonDisabled(): boolean {
    return this.disabled || this.isLoading() || this.isCompletedState();
  }

  /**
   * Check if this is a completed state (no action needed)
   */
  private isCompletedState(): boolean {
    // First checkpoint that's checked in shows "CHECKED IN" - no action
    if (this.checkpoint.isFirst && this.checkpoint.status === 'checked-in') {
      return true;
    }

    // Last checkpoint that's checked in shows "FINISHED" - no action
    if (this.checkpoint.isLast && this.checkpoint.status === 'checked-in') {
      return true;
    }

    return false;
  }


  /**
   * Handle button click - determine and execute the appropriate action
   */
  async onButtonClick() {
    if (this.isButtonDisabled) return;

    const state = this.buttonState();
    const checkpoint = this.checkpointSignal();

    console.log('Button clicked! State:', state);
    console.log('Button data:', {
      checkpoint: checkpoint,
      trackUid: this.trackUid,
      participantUid: this.participantUid,
      startNumber: this.startNumber
    });

    // For checked-in state, cycle to next action after performing current one
    if (checkpoint?.status === 'checked-in' && !checkpoint?.isFirst && !checkpoint?.isLast) {
      // Perform the current action
      switch (state) {
        case 'check-out':
          await this.handleCheckOut();
          break;
        case 'undo-checkin':
          await this.onUndoCheckInClick();
          break;
      }

      // Cycle to next action for next click
      this.currentActionIndex.set(this.currentActionIndex() + 1);
      return;
    }

    // For all other states, perform the action normally
    switch (state) {
      case 'start':
        await this.handleStart();
        break;
      case 'check-in':
        await this.handleCheckIn();
        break;
      case 'check-out':
        await this.handleCheckOut();
        break;
      case 'undo-checkout':
        await this.handleUndoCheckout();
        break;
      case 'undo-checkin':
        await this.onUndoCheckInClick();
        break;
      default:
        console.warn('Unknown button state:', state);
    }
  }

  /**
   * Handle undo check-in action
   */
  async onUndoCheckInClick() {
    if (this.isButtonDisabled) return;

    const checkpoint = this.checkpointSignal();
    if (!checkpoint) return;

    const confirmed = confirm(`Are you sure you want to undo the check-in at ${checkpoint.name}?`);
    if (!confirmed) return;

    this.isLoading.set(true);

    // Optimistic update - update UI immediately
    this.updateCheckpointStatusOptimistically(checkpoint, 'not-visited');

    this.actionCompleted.emit({
      action: 'undo-check-in',
      checkpoint: checkpoint,
      response: { success: true }
    });

    // Make the actual request in the background
    this.performUndoCheckIn().catch(error => {
      console.error('Undo check-in failed:', error);
      // Error message will be shown by feedbackInterceptor
      // Revert optimistic update on error
      this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');
    }).finally(() => {
      this.isLoading.set(false);
    });
  }

  /**
   * Handle start action (first checkpoint check-in)
   */
  private async handleStart() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) return;

    this.isLoading.set(true);

    if (checkpoint.status === 'not-visited') {
      // First time: check in and then check out
      this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');

      this.actionCompleted.emit({
        action: 'start',
        checkpoint: checkpoint,
        response: { success: true }
      });

      // Perform check-in then check-out in background
      this.performStartSequence().catch(error => {
        console.error('Start sequence failed:', error);
        // Error message will be shown by feedbackInterceptor
        this.updateCheckpointStatusOptimistically(checkpoint, 'not-visited');
      }).finally(() => {
        this.isLoading.set(false);
      });
    } else {
      // Already checked in: just perform check-out in background

      this.performCheckOut().catch(error => {
        console.error('Check-out failed:', error);
        // Error message will be shown by feedbackInterceptor
      }).finally(() => {
        this.isLoading.set(false);
      });
    }
  }

  /**
   * Handle regular check-in
   */
  private async handleCheckIn() {
    this.isLoading.set(true);

    const checkpoint = this.checkpointSignal();
    if (!checkpoint) return;

    // Optimistic update - update UI immediately
    this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');

    this.actionCompleted.emit({
      action: 'check-in',
      checkpoint: checkpoint,
      response: { success: true }
    });

    // Make the actual request in the background
    this.performCheckIn().catch(error => {
      console.error('Check-in failed:', error);
      // Error message will be shown by feedbackInterceptor
      // Revert optimistic update on error
      this.updateCheckpointStatusOptimistically(checkpoint, 'not-visited');
    }).finally(() => {
      this.isLoading.set(false);
    });
  }

  /**
   * Handle check-out
   */
  private async handleCheckOut() {
    this.isLoading.set(true);

    const checkpoint = this.checkpointSignal();
    if (!checkpoint) return;

    // Optimistic update - update UI immediately
    this.updateCheckpointStatusOptimistically(checkpoint, 'checked-out');

    this.actionCompleted.emit({
      action: 'check-out',
      checkpoint: checkpoint,
      response: { success: true }
    });

    // Make the actual request in the background
    this.performCheckOut().catch(error => {
      console.error('Check-out failed:', error);
      // Error message will be shown by feedbackInterceptor
      // Revert optimistic update on error
      this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');
    }).finally(() => {
      this.isLoading.set(false);
    });
  }

  /**
   * Handle undo checkout
   */
  private async handleUndoCheckout() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) return;

    const confirmed = confirm(`Are you sure you want to undo the check-out from ${checkpoint.name}?`);
    if (!confirmed) return;

    this.isLoading.set(true);

    // Optimistic update - update UI immediately
    this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');

    this.actionCompleted.emit({
      action: 'undo-checkout',
      checkpoint: checkpoint,
      response: { success: true }
    });

    // Make the actual request in the background
    this.performUndoCheckout().catch(error => {
      console.error('Undo check-out failed:', error);
      // Error message will be shown by feedbackInterceptor
      // Revert optimistic update on error
      this.updateCheckpointStatusOptimistically(checkpoint, 'checked-out');
    }).finally(() => {
      this.isLoading.set(false);
    });
  }

  /**
   * Update checkpoint status optimistically (immediate UI update)
   */
  private updateCheckpointStatusOptimistically(checkpoint: CheckpointButtonData, newStatus: 'checked-in' | 'checked-out' | 'not-visited') {
    const updatedCheckpoint = { ...checkpoint, status: newStatus };
    this.checkpointSignal.set(updatedCheckpoint);
    console.log(`Optimistic update: ${checkpoint.name} -> ${newStatus}`);
  }

  /**
   * Perform check-out API call
   */
  private async performCheckOut() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) throw new Error('No checkpoint data available');

    const url = `${environment.backend_url}randonneur/${this.participantUid}/track/${this.trackUid}/startnumber/${this.startNumber}/checkpoint/${checkpoint.checkpoint_uid}/checkoutFrom`;

    console.log('Making check-out request to:', url);
    console.log('Request data:', {
      participantUid: this.participantUid,
      trackUid: this.trackUid,
      startNumber: this.startNumber,
      checkpointUid: checkpoint.checkpoint_uid
    });

    const response = await firstValueFrom(
      this.http.put<ButtonActionResponse>(url, {})
    );

    console.log('Check-out response:', response);
    return response;
  }

  /**
   * Perform undo checkout API call
   */
  private async performUndoCheckout() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) throw new Error('No checkpoint data available');

    const url = `${environment.backend_url}randonneur/${this.participantUid}/track/${this.trackUid}/startnumber/${this.startNumber}/checkpoint/${checkpoint.checkpoint_uid}/undocheckoutFrom`;

    console.log('Making undo check-out request to:', url);
    console.log('Request data:', {
      participantUid: this.participantUid,
      trackUid: this.trackUid,
      startNumber: this.startNumber,
      checkpointUid: checkpoint.checkpoint_uid
    });

    const response = await firstValueFrom(
      this.http.put<ButtonActionResponse>(url, {})
    );

    console.log('Undo check-out response:', response);
    return response;
  }

  /**
   * Perform start sequence: check-in then check-out for first checkpoint
   */
  private async performStartSequence() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) throw new Error('No checkpoint data available');

    console.log(`Performing start sequence for: ${checkpoint.name}`);

    try {
      // Step 1: Check in
      console.log('Step 1: Checking in...');
      await this.performCheckIn();

      // Step 2: Check out immediately after
      console.log('Step 2: Checking out...');
      await this.performCheckOut();

      console.log('Start sequence completed successfully');
    } catch (error) {
      console.error('Start sequence failed:', error);
      throw error;
    }
  }

  /**
   * Perform undo check-in API call
   */
  private async performUndoCheckIn() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) throw new Error('No checkpoint data available');

    const url = `${environment.backend_url}randonneur/${this.participantUid}/track/${this.trackUid}/startnumber/${this.startNumber}/checkpoint/${checkpoint.checkpoint_uid}/rollback`;

    console.log('Making undo check-in request to:', url);
    console.log('Request data:', {
      participantUid: this.participantUid,
      trackUid: this.trackUid,
      startNumber: this.startNumber,
      checkpointUid: checkpoint.checkpoint_uid
    });

    const response = await firstValueFrom(
      this.http.put<ButtonActionResponse>(url, {})
    );

    console.log('Undo check-in response:', response);
    return response;
  }

  /**
   * Perform check-in API call (shared by start and regular check-in)
   */
  private async performCheckIn() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) throw new Error('No checkpoint data available');

    const url = `${environment.backend_url}randonneur/${this.participantUid}/track/${this.trackUid}/startnumber/${this.startNumber}/checkpoint/${checkpoint.checkpoint_uid}/stamp`;

    // Get current location for stamp
    const position = await this.getCurrentLocation();
    const params = position ? `?lat=${position.lat}&long=${position.lng}` : '';

    console.log('Making check-in request to:', `${url}${params}`);
    console.log('Request data:', {
      participantUid: this.participantUid,
      trackUid: this.trackUid,
      startNumber: this.startNumber,
      checkpointUid: checkpoint.checkpoint_uid,
      position: position
    });

    const response = await firstValueFrom(
      this.http.post<ButtonActionResponse>(`${url}${params}`, {})
    );

    console.log('Check-in response:', response);
    return response;
  }

  /**
   * Get current location for check-in
   */
  private async getCurrentLocation(): Promise<{lat: number, lng: number} | null> {
    try {
      if (!navigator.geolocation) {
        console.warn('Geolocation not supported');
        return null;
      }

      const position = await new Promise<GeolocationPosition>((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 60000
        });
      });

      return {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
    } catch (error) {
      console.warn('Could not get location:', error);
      return null;
    }
  }

  /**
   * Get loading state
   */
  get loading(): boolean {
    return this.isLoading();
  }
}
