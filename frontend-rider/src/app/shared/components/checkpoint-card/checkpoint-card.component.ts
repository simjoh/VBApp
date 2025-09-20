import { Component, Input, Output, EventEmitter, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CheckpointButtonComponent } from '../checkpoint-button/checkpoint-button.component';
import { TranslationPipe } from '../../pipes/translation.pipe';
import { AssetService } from '../../../core/services/asset.service';

export interface CheckpointData {
  checkpoint_uid: string;
  name: string;
  distance: number;
  toNext: number;
  opens: string;
  closes: string;
  service: string;
  time: string;
  logoFileName?: string; // Keep for backward compatibility
  status: 'checked-in' | 'checked-out' | 'not-visited';
  timestamp?: string;
  checkoutTimestamp?: string;
  site?: {
    image?: string;
    adress?: string;
    place?: string;
    description?: string;
  };
}

@Component({
  selector: 'app-checkpoint-card',
  standalone: true,
  imports: [CommonModule, CheckpointButtonComponent, TranslationPipe],
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
  @Input() disabled: boolean = false;

  @Output() actionCompleted = new EventEmitter<any>();

  private assetService = inject(AssetService);

  get logoUrl(): string | null {
    // Prioritize site.image from backend
    if (this.checkpoint.site?.image) {
      return this.assetService.getCheckpointLogoFromBackendPath(this.checkpoint.site.image);
    }

    // Fallback to logoFileName for backward compatibility
    if (this.checkpoint.logoFileName) {
      return this.assetService.getCheckpointLogoUrl(this.checkpoint.logoFileName);
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
    // Checkpoint action completed
    this.actionCompleted.emit(event);
  }
}
