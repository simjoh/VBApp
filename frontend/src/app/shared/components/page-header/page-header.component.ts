import { Component, Input } from '@angular/core';

export interface PageHeaderConfig {
  icon: string;
  title: string;
  description: string;
}

@Component({
  selector: 'app-page-header',
  templateUrl: './page-header.component.html',
  styleUrls: ['./page-header.component.scss']
})
export class PageHeaderComponent {
  @Input() config!: PageHeaderConfig;
}
