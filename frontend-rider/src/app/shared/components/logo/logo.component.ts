import { Component, Input, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AssetService } from '../../../core/services/asset.service';

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

  private assetService = inject(AssetService);

  get logoUrl(): string {
    return this.assetService.getLogoUrl();
  }
}
