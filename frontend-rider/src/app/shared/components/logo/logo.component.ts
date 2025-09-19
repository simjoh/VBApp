import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-logo',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './logo.component.html',
  styleUrl: './logo.component.scss'
})
export class LogoComponent {
  @Input() height: number = 120;
  @Input() width: number = 120;
  @Input() rolling: boolean = false;

  get logoUrl(): string {
    // Use the ebrevet-prod.svg logo
    return '/assets/ebrevet-prod.svg';
  }
}
