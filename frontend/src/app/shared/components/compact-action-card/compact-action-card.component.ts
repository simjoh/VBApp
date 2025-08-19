import { Component, Input, Output, EventEmitter } from '@angular/core';

export interface CompactActionCardConfig {
  icon: string;
  title: string;
  description?: string;
  isActive?: boolean;
  action?: string;
}

@Component({
  selector: 'app-compact-action-card',
  templateUrl: './compact-action-card.component.html',
  styleUrls: ['./compact-action-card.component.scss']
})
export class CompactActionCardComponent {
  @Input() config!: CompactActionCardConfig;
  @Output() cardClick = new EventEmitter<string>();

  onCardClick() {
    if (this.config.action) {
      this.cardClick.emit(this.config.action);
    }
  }
}
