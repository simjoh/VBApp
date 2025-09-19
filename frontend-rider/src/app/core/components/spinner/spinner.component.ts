import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PendingRequestsService } from '../../services/pending-requests.service';

@Component({
  selector: 'app-spinner',
  standalone: true,
  imports: [CommonModule],
  template: `
    @if (pendingRequestsService.count$()) {
      <div class="spinner-overlay">
        <div class="spinner">
          <div class="spinner-circle"></div>
          <div class="spinner-text">Loading...</div>
        </div>
      </div>
    }
  `,
  styles: [`
    .spinner-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .spinner {
      display: flex;
      flex-direction: column;
      align-items: center;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .spinner-circle {
      width: 40px;
      height: 40px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #007bff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    .spinner-text {
      margin-top: 1rem;
      color: #333;
      font-size: 0.9rem;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `]
})
export class SpinnerComponent {
  protected readonly pendingRequestsService = inject(PendingRequestsService);
}
