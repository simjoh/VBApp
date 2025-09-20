import { Component, inject, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LanguageService, SupportedLanguage } from '../../../core/services/language.service';

@Component({
  selector: 'app-flag-language-selector',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="language-selector">
      <div class="current-language"
           [title]="getCurrentLanguageConfig().nativeName"
           (click)="toggleDropdown()">
        <span class="flag">{{ getCurrentLanguageConfig().flag }}</span>
        <span class="language-code">{{ getCurrentLanguageConfig().code.toUpperCase() }}</span>
        <i class="pi pi-chevron-down arrow" [class.rotated]="isDropdownOpen"></i>
      </div>

      <div class="language-dropdown" [class.show]="isDropdownOpen">
        @for (lang of languageService.getSupportedLanguages(); track lang.code) {
          <button
            class="language-option"
            [class.active]="lang.code === languageService.getCurrentLanguage()"
            (click)="selectLanguage(lang.code)"
            [title]="lang.nativeName + ' (' + lang.code.toUpperCase() + ')'"
            [attr.aria-label]="lang.nativeName">
            <span class="flag">{{ lang.flag }}</span>
            @if (lang.code === languageService.getCurrentLanguage()) {
              <i class="pi pi-check check-icon"></i>
            }
          </button>
        }
      </div>

      <div
        class="dropdown-overlay"
        [class.show]="isDropdownOpen"
        (click)="closeDropdown()">
      </div>
    </div>
  `,
  styles: [`
    .language-selector {
      position: relative;
      display: inline-block;
    }

    .current-language {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 0.75rem;
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s ease;
      min-width: 80px;
      user-select: none;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);

      &:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
      }

      .flag {
        font-size: 1.2rem;
        line-height: 1;
      }

      .language-code {
        font-size: 0.75rem;
        font-weight: 600;
        color: #374151;
      }

      .arrow {
        font-size: 0.75rem;
        color: #6b7280;
        transition: transform 0.2s ease;

        &.rotated {
          transform: rotate(180deg);
        }
      }
    }

    .language-dropdown {
      position: absolute;
      top: calc(100% + 0.5rem);
      right: 0;
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(10px);
      z-index: 1000;
      display: flex;
      flex-direction: column;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.2s ease;

      &.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
      }
    }

    .language-option {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      width: 60px;
      height: 50px;
      border: none;
      background: none;
      cursor: pointer;
      transition: all 0.15s ease;
      border-bottom: 1px solid #f3f4f6;

      &:first-child {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
      }

      &:last-child {
        border-bottom: none;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
      }

      &:hover {
        background: #f8fafc;
      }

      &.active {
        background: #eff6ff;
      }

      .flag {
        font-size: 1.5rem;
        line-height: 1;
      }

      .check-icon {
        position: absolute;
        top: 4px;
        right: 4px;
        font-size: 0.7rem;
        color: #10b981;
        background: white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }
    }

    .dropdown-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.2s ease;

      &.show {
        opacity: 1;
        visibility: visible;
      }
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
      .language-dropdown {
        right: 0;
        width: auto;
      }

      .current-language {
        min-width: 60px;
        padding: 0.3rem 0.5rem;

        .flag {
          font-size: 1rem;
        }

        .language-code {
          font-size: 0.65rem;
        }

        .arrow {
          font-size: 0.65rem;
        }
      }

      .language-option {
        width: 50px;
        height: 45px;

        .flag {
          font-size: 1.3rem;
        }

        .check-icon {
          width: 14px;
          height: 14px;
          font-size: 0.6rem;
        }
      }
    }
  `]
})
export class FlagLanguageSelectorComponent {
  languageService = inject(LanguageService);
  isDropdownOpen = false;

  getCurrentLanguageConfig() {
    return this.languageService.currentLanguageConfig();
  }

  toggleDropdown() {
    this.isDropdownOpen = !this.isDropdownOpen;
  }

  closeDropdown() {
    this.isDropdownOpen = false;
  }

  selectLanguage(language: string): void {
    this.languageService.setLanguage(language as SupportedLanguage);
    this.closeDropdown();
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: Event) {
    // Close dropdown when clicking outside
    const target = event.target as HTMLElement;
    if (!target.closest('.language-selector')) {
      this.closeDropdown();
    }
  }
}
