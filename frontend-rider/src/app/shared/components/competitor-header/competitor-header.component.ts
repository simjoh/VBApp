import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LogoComponent } from '../logo/logo.component';

@Component({
  selector: 'app-competitor-header',
  standalone: true,
  imports: [CommonModule, LogoComponent],
  templateUrl: './competitor-header.component.html',
  styleUrl: './competitor-header.component.scss'
})
export class CompetitorHeaderComponent {
  @Input() startNumber: string = '#123';
  @Input() riderName: string = 'John Andersson';
  @Output() logout = new EventEmitter<void>();

  onLogout() {
    this.logout.emit();
  }
}
