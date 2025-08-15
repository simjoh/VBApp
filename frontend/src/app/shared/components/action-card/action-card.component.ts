import { Component, Input, Output, EventEmitter } from '@angular/core';

export interface ActionCardConfig {
  icon: string;
  title: string;
  description: string;
  isActive?: boolean;
  action?: string;
}

@Component({
  selector: 'app-action-card',
  templateUrl: './action-card.component.html',
  styleUrls: ['./action-card.component.scss']
})
export class ActionCardComponent {
  @Input() config!: ActionCardConfig;
  @Output() cardClick = new EventEmitter<string>();

  onCardClick() {
    if (this.config.action) {
      this.cardClick.emit(this.config.action);
    }
  }
}
