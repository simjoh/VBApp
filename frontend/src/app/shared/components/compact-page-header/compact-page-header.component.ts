import { Component, Input } from '@angular/core';

export interface CompactPageHeaderConfig {
  icon: string;
  title: string;
  description?: string;
  showDescription?: boolean;
}

@Component({
  selector: 'app-compact-page-header',
  templateUrl: './compact-page-header.component.html',
  styleUrls: ['./compact-page-header.component.scss']
})
export class CompactPageHeaderComponent {
  @Input() config!: CompactPageHeaderConfig;

  get showDescription(): boolean {
    return this.config.showDescription !== false && !!this.config.description;
  }
}
