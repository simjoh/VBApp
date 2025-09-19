import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MessageService, Message } from '../../services/message.service';

@Component({
  selector: 'app-message-toast',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="message-container">
      @for (message of messageService.messages$(); track message.id) {
        <div
          class="message-toast"
          [class]="'message-' + message.type"
          (click)="removeMessage(message.id)"
        >
          <div class="message-icon">
            @switch (message.type) {
              @case ('success') {
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              }
              @case ('error') {
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              }
              @case ('warning') {
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              }
              @case ('info') {
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
              }
            }
          </div>

          <div class="message-content">
            <div class="message-title">{{ message.title }}</div>
            <div class="message-text">{{ message.message }}</div>
          </div>

          <button
            class="message-close"
            (click)="removeMessage(message.id); $event.stopPropagation()"
            aria-label="Close message"
          >
            <svg viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      }
    </div>
  `,
  styles: [`
    .message-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 400px;
    }

    .message-toast {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 16px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      cursor: pointer;
      transition: all 0.3s ease;
      animation: slideIn 0.3s ease;
    }

    .message-toast:hover {
      transform: translateX(-5px);
    }

    .message-success {
      background: #d1fae5;
      border-left: 4px solid #10b981;
      color: #065f46;
    }

    .message-error {
      background: #fee2e2;
      border-left: 4px solid #ef4444;
      color: #991b1b;
    }

    .message-warning {
      background: #fef3c7;
      border-left: 4px solid #f59e0b;
      color: #92400e;
    }

    .message-info {
      background: #dbeafe;
      border-left: 4px solid #3b82f6;
      color: #1e40af;
    }

    .message-icon {
      flex-shrink: 0;
      margin-top: 2px;
    }

    .icon {
      width: 20px;
      height: 20px;
    }

    .message-content {
      flex: 1;
      min-width: 0;
    }

    .message-title {
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 4px;
    }

    .message-text {
      font-size: 0.85rem;
      opacity: 0.9;
      line-height: 1.4;
    }

    .message-close {
      flex-shrink: 0;
      background: none;
      border: none;
      cursor: pointer;
      padding: 4px;
      border-radius: 4px;
      opacity: 0.7;
      transition: opacity 0.2s ease;
    }

    .message-close:hover {
      opacity: 1;
    }

    .message-close svg {
      width: 16px;
      height: 16px;
    }

    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    @media (max-width: 480px) {
      .message-container {
        left: 10px;
        right: 10px;
        max-width: none;
      }
    }
  `]
})
export class MessageToastComponent {
  protected readonly messageService = inject(MessageService);

  removeMessage(id: string): void {
    this.messageService.removeMessage(id);
  }
}
