import { Component, Input, Output, EventEmitter, inject, signal, computed, OnChanges, OnDestroy, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { MessageService } from '../../../core/services/message.service';
import { TranslationService } from '../../../core/services/translation.service';
import { TranslationPipe } from '../../pipes/translation.pipe';
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
  imports: [CommonModule, TranslationPipe],
  templateUrl: './checkpoint-button.component.html',
  styleUrl: './checkpoint-button.component.scss'
})
export class CheckpointButtonComponent implements OnChanges, OnDestroy {
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
  private translationService = inject(TranslationService);

  // Loading state
  private isLoading = signal(false);

  // Reactive signals for input properties
  private checkpointSignal = signal<CheckpointButtonData | null>(null);

  // Track which action to show for checked-in state
  private currentActionIndex = signal(0);

  // Timer and progress bar state
  private timerInterval: any = null;
  private timeAtCheckpoint = signal<number>(0); // Time in seconds
  private maxStayTime = 30 * 60; // 30 minutes in seconds
  private checkInStartTime: number | null = null; // Local check-in time
  private wasClickedForCheckIn = signal<boolean>(false); // Track if this checkpoint was clicked for check-in

  // Computed properties for timer display
  get timerDisplay(): string {
    const totalSeconds = this.timeAtCheckpoint();
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  }

  get progressPercentage(): number {
    return Math.min((this.timeAtCheckpoint() / this.maxStayTime) * 100, 100);
  }

  get isOverTime(): boolean {
    return this.timeAtCheckpoint() > this.maxStayTime;
  }

  get showTimer(): boolean {
    const checkpoint = this.checkpointSignal();
    const wasClicked = this.wasClickedForCheckIn();
    const shouldShow = checkpoint?.status === 'checked-in' && wasClicked;
    // Show timer check
    return shouldShow;
  }

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
      const newCheckpoint = changes['checkpoint'].currentValue;
      this.checkpointSignal.set(newCheckpoint);

      // Start/stop timer based on status
      this.updateTimer(newCheckpoint);
    }
  }

  private updateTimer(checkpoint: CheckpointButtonData) {
    // Updating timer for checkpoint

    // Clear existing timer
    if (this.timerInterval) {
      clearInterval(this.timerInterval);
      this.timerInterval = null;
    }

    // Reset click flag when status changes away from checked-in
    if (checkpoint.status !== 'checked-in') {
      this.wasClickedForCheckIn.set(false);
    }

    // Start timer if checked in AND was clicked for check-in
    if (checkpoint.status === 'checked-in' && this.wasClickedForCheckIn()) {
      // Starting timer for checked-in checkpoint that was clicked
      this.startTimerFromZero();
    } else {
      // Stopping timer - not checked in or not clicked for check-in
      this.timeAtCheckpoint.set(0);
      this.checkInStartTime = null;
    }
  }

  private startTimerFromZero() {
    // Starting timer from 0

    // Set the start time to now
    this.checkInStartTime = new Date().getTime();
    this.timeAtCheckpoint.set(0);

    // Update timer every second
    this.timerInterval = setInterval(() => {
      if (this.checkInStartTime) {
        const now = new Date().getTime();
        const timeDiff = Math.floor((now - this.checkInStartTime) / 1000);
        this.timeAtCheckpoint.set(Math.max(0, timeDiff));
        // Timer running
      }
    }, 1000);
  }

  ngOnDestroy() {
    // Clean up timer when component is destroyed
    if (this.timerInterval) {
      clearInterval(this.timerInterval);
      this.timerInterval = null;
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
          return this.translationService.translate('checkpoint.checkIn');
        } else if (checkpoint?.status === 'checked-in') {
          return this.translationService.translate('checkpoint.checkedIn');
        } else if (checkpoint?.status === 'checked-out') {
          return this.translationService.translate('checkpoint.checkedIn');
        }
        return this.translationService.translate('checkpoint.checkIn');
      case 'finish':
        return this.translationService.translate('checkpoint.finished');
      case 'check-in':
        return this.translationService.translate('checkpoint.checkIn');
      case 'check-out':
        return this.translationService.translate('checkpoint.checkOut');
      case 'undo-checkout':
        return this.translationService.translate('checkpoint.undoCheckOut');
      case 'undo-checkin':
        return this.translationService.translate('checkpoint.undoCheckIn');
      default:
        return this.translationService.translate('checkpoint.checkIn');
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

    // Button clicked

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
        // Unknown button state
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

    // Reset click flag when undoing check-in
    this.wasClickedForCheckIn.set(false);

    // Optimistic update - update UI immediately
    this.updateCheckpointStatusOptimistically(checkpoint, 'not-visited');

    this.actionCompleted.emit({
      action: 'undo-check-in',
      checkpoint: checkpoint,
      response: { success: true }
    });

    // Make the actual request in the background
    this.performUndoCheckIn().catch(error => {
      // Undo check-in failed
      // Error message will be shown by feedbackInterceptor
      // Revert optimistic update on error
      this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');
      // Restore click flag on error
      this.wasClickedForCheckIn.set(true);
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
      // Mark this checkpoint as clicked for check-in
      this.wasClickedForCheckIn.set(true);

      this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');

      this.actionCompleted.emit({
        action: 'start',
        checkpoint: checkpoint,
        response: { success: true }
      });

      // Perform check-in then check-out in background
      this.performStartSequence().catch(error => {
        // Start sequence failed
        // Error message will be shown by feedbackInterceptor
        this.updateCheckpointStatusOptimistically(checkpoint, 'not-visited');
        // Reset the click flag on error
        this.wasClickedForCheckIn.set(false);
      }).finally(() => {
        this.isLoading.set(false);
      });
    } else {
      // Already checked in: just perform check-out in background

      this.performCheckOut().catch(error => {
        // Check-out failed
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

    // Mark this checkpoint as clicked for check-in
    this.wasClickedForCheckIn.set(true);

    // Optimistic update - update UI immediately
    this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');

    this.actionCompleted.emit({
      action: 'check-in',
      checkpoint: checkpoint,
      response: { success: true }
    });

    // Make the actual request in the background
    this.performCheckIn().catch(error => {
      // Check-in failed
      // Error message will be shown by feedbackInterceptor
      // Revert optimistic update on error
      this.updateCheckpointStatusOptimistically(checkpoint, 'not-visited');
      // Reset the click flag on error
      this.wasClickedForCheckIn.set(false);
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

    // Reset click flag when checking out
    this.wasClickedForCheckIn.set(false);

    // Optimistic update - update UI immediately
    this.updateCheckpointStatusOptimistically(checkpoint, 'checked-out');

    this.actionCompleted.emit({
      action: 'check-out',
      checkpoint: checkpoint,
      response: { success: true }
    });

    // Make the actual request in the background
    this.performCheckOut().catch(error => {
      // Check-out failed
      // Error message will be shown by feedbackInterceptor
      // Revert optimistic update on error
      this.updateCheckpointStatusOptimistically(checkpoint, 'checked-in');
      // Restore click flag on error
      this.wasClickedForCheckIn.set(true);
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
      // Undo check-out failed
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
    // Optimistic update
  }

  /**
   * Perform check-out API call
   */
  private async performCheckOut() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) throw new Error('No checkpoint data available');

    const url = `${environment.backend_url}randonneur/${this.participantUid}/track/${this.trackUid}/startnumber/${this.startNumber}/checkpoint/${checkpoint.checkpoint_uid}/checkoutFrom`;

    // Making check-out request

    const response = await firstValueFrom(
      this.http.put<ButtonActionResponse>(url, {})
    );

    // Check-out response received
    return response;
  }

  /**
   * Perform undo checkout API call
   */
  private async performUndoCheckout() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) throw new Error('No checkpoint data available');

    const url = `${environment.backend_url}randonneur/${this.participantUid}/track/${this.trackUid}/startnumber/${this.startNumber}/checkpoint/${checkpoint.checkpoint_uid}/undocheckoutFrom`;

    // Making undo check-out request

    const response = await firstValueFrom(
      this.http.put<ButtonActionResponse>(url, {})
    );

    // Undo check-out response received
    return response;
  }

  /**
   * Perform start sequence: check-in then check-out for first checkpoint
   */
  private async performStartSequence() {
    const checkpoint = this.checkpointSignal();
    if (!checkpoint) throw new Error('No checkpoint data available');

    // Performing start sequence

    try {
      // Step 1: Check in
      await this.performCheckIn();

      // Step 2: Check out immediately after
      await this.performCheckOut();

      // Start sequence completed successfully
    } catch (error) {
      // Start sequence failed
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

    // Making undo check-in request

    const response = await firstValueFrom(
      this.http.put<ButtonActionResponse>(url, {})
    );

    // Undo check-in response received
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

    // Making check-in request

    const response = await firstValueFrom(
      this.http.post<ButtonActionResponse>(`${url}${params}`, {})
    );

    // Check-in response received
    return response;
  }

  /**
   * Get current location for check-in
   */
  private async getCurrentLocation(): Promise<{lat: number, lng: number} | null> {
    try {
      if (!navigator.geolocation) {
        // Geolocation not supported
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
      // Could not get location
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
