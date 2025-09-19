import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { environment } from '../../../../environments/environment';

export interface CheckpointData {
  name: string;
  distance: number;
  toNext: number;
  opens: string;
  closes: string;
  service: string;
  time: string;
  logoFileName?: string;
  status: 'not-visited' | 'checked-in' | 'open' | 'closed';
}

@Component({
  selector: 'app-checkpoint-card',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './checkpoint-card.component.html',
  styleUrl: './checkpoint-card.component.scss'
})
export class CheckpointCardComponent {
  @Input() checkpoint!: CheckpointData;

  get logoUrl(): string {
    if (this.checkpoint.logoFileName) {
      return `${environment.pictureurl}/${this.checkpoint.logoFileName}`;
    }
    return '';
  }

  get statusText(): string {
    switch (this.checkpoint.status) {
      case 'checked-in':
        return 'CHECKED IN';
      case 'open':
        return 'OPEN';
      case 'closed':
        return 'CLOSED';
      case 'not-visited':
      default:
        return '';
    }
  }

  get statusClass(): string {
    switch (this.checkpoint.status) {
      case 'checked-in':
        return 'status-checked-in';
      case 'open':
        return 'status-open';
      case 'closed':
        return 'status-closed';
      case 'not-visited':
      default:
        return 'status-not-visited';
    }
  }
}
