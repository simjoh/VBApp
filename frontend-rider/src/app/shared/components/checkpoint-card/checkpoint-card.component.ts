import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { environment } from '../../../../environments/environment';
import { CheckpointButtonComponent } from '../checkpoint-button/checkpoint-button.component';

export interface CheckpointData {
  checkpoint_uid: string;
  name: string;
  distance: number;
  toNext: number;
  opens: string;
  closes: string;
  service: string;
  time: string;
  logoFileName?: string;
  status: 'checked-in' | 'checked-out' | 'not-visited';
  timestamp?: string;
  checkoutTimestamp?: string;
}

@Component({
  selector: 'app-checkpoint-card',
  standalone: true,
  imports: [CommonModule, CheckpointButtonComponent],
  templateUrl: './checkpoint-card.component.html',
  styleUrl: './checkpoint-card.component.scss'
})
export class CheckpointCardComponent {
  @Input({ required: true }) checkpoint!: CheckpointData;
  @Input({ required: true }) trackUid!: string;
  @Input({ required: true }) participantUid!: string;
  @Input({ required: true }) startNumber!: string;
  @Input({ required: true }) isFirst!: boolean;
  @Input({ required: true }) isLast!: boolean;
  @Input({ required: true }) totalCheckpoints!: number;

  @Output() actionCompleted = new EventEmitter<any>();

  get logoUrl(): string | null {
    if (this.checkpoint.logoFileName) {
      return `${environment.pictureurl}/${this.checkpoint.logoFileName}`;
    }
    return null;
  }

  get statusClass(): string {
    switch (this.checkpoint.status) {
      case 'checked-in':
        return 'status-checked-in';
      case 'checked-out':
        return 'status-checked-out';
      case 'not-visited':
      default:
        return 'status-not-visited';
    }
  }

  /**
   * Handle action completed from checkpoint button
   */
  onActionCompleted(event: any) {
    console.log('Checkpoint action completed:', event);
    this.actionCompleted.emit(event);
  }
}
