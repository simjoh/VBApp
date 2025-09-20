import { Component, signal, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet } from '@angular/router';
import { SpinnerComponent } from './core/components/spinner/spinner.component';
import { MessageToastComponent } from './core/components/message-toast/message-toast.component';
import { LanguageService } from './core/services/language.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, SpinnerComponent, MessageToastComponent],
  templateUrl: './app.html',
  styleUrl: './app.scss'
})
export class App implements OnInit {
  protected readonly title = signal('Rider App');

  private languageService = inject(LanguageService);

  constructor() {
    // App component initialization
  }

  ngOnInit() {
    // Initialize language service
    try {
      this.languageService.setLanguage(this.languageService.getCurrentLanguage());
    } catch (error) {
      console.error('Language service initialization failed:', error);
    }
  }
}
