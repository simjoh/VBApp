import { Injectable, signal } from '@angular/core';

export interface Message {
  id: string;
  type: 'success' | 'error' | 'info' | 'warning';
  title: string;
  message: string;
  duration?: number;
  timestamp: number;
}

@Injectable({
  providedIn: 'root'
})
export class MessageService {
  private _messages = signal<Message[]>([]);

  // Read-only signal for components
  readonly messages$ = this._messages.asReadonly();

  showSuccess(title: string, message: string, duration: number = 5000): void {
    this.addMessage({
      id: this.generateId(),
      type: 'success',
      title,
      message,
      duration,
      timestamp: Date.now()
    });
  }

  showError(title: string, message: string, duration: number = 7000): void {
    this.addMessage({
      id: this.generateId(),
      type: 'error',
      title,
      message,
      duration,
      timestamp: Date.now()
    });
  }

  showInfo(title: string, message: string, duration: number = 4000): void {
    this.addMessage({
      id: this.generateId(),
      type: 'info',
      title,
      message,
      duration,
      timestamp: Date.now()
    });
  }

  showWarning(title: string, message: string, duration: number = 6000): void {
    this.addMessage({
      id: this.generateId(),
      type: 'warning',
      title,
      message,
      duration,
      timestamp: Date.now()
    });
  }

  removeMessage(id: string): void {
    this._messages.update(messages => messages.filter(msg => msg.id !== id));
  }

  clearAll(): void {
    this._messages.set([]);
  }

  private addMessage(message: Message): void {
    this._messages.update(messages => [...messages, message]);

    // Auto-remove message after duration
    if (message.duration && message.duration > 0) {
      setTimeout(() => {
        this.removeMessage(message.id);
      }, message.duration);
    }
  }

  private generateId(): string {
    return Math.random().toString(36).substr(2, 9);
  }
}
