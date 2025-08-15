import { Component, Input, Output, EventEmitter, ContentChild, TemplateRef } from '@angular/core';
import { PageHeaderConfig } from '../page-header/page-header.component';
import { ActionCardConfig } from '../action-card/action-card.component';

@Component({
  selector: 'app-page-layout',
  templateUrl: './page-layout.component.html',
  styleUrls: ['./page-layout.component.scss']
})
export class PageLayoutComponent {
  @Input() headerConfig!: PageHeaderConfig;
  @Input() actionCards: ActionCardConfig[] = [];
  @Input() showActionCards = true;
  @Output() actionCardClick = new EventEmitter<string>();
  
  @ContentChild('content') contentTemplate?: TemplateRef<any>;

  onActionCardClick(action: string) {
    this.actionCardClick.emit(action);
  }
}
