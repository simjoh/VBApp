import { Injectable, signal, computed } from '@angular/core';

export type SupportedLanguage = 'sv' | 'en';

export interface LanguageConfig {
  code: SupportedLanguage;
  name: string;
  nativeName: string;
  flag: string;
}

@Injectable({
  providedIn: 'root'
})
export class LanguageService {
  private readonly SUPPORTED_LANGUAGES: LanguageConfig[] = [
    { code: 'sv', name: 'Swedish', nativeName: 'Svenska', flag: '🇸🇪' },
    { code: 'en', name: 'English', nativeName: 'English', flag: '🇬🇧' }
  ];

  private readonly DEFAULT_LANGUAGE: SupportedLanguage = 'sv';
  private readonly STORAGE_KEY = 'frontend-language';

  // Current language signal
  private _currentLanguage = signal<SupportedLanguage>(this.getStoredLanguage());

  // Public readonly signals
  readonly currentLanguage = this._currentLanguage.asReadonly();
  readonly supportedLanguages = this.SUPPORTED_LANGUAGES;
  readonly currentLanguageConfig = computed(() =>
    this.SUPPORTED_LANGUAGES.find(lang => lang.code === this._currentLanguage()) ||
    this.SUPPORTED_LANGUAGES[0]
  );

  constructor() {
    this.setLanguage(this._currentLanguage());
  }

  setLanguage(language: SupportedLanguage): void {
    this._currentLanguage.set(language);
    localStorage.setItem(this.STORAGE_KEY, language);
    document.documentElement.lang = language;
  }

  getCurrentLanguage(): SupportedLanguage {
    return this._currentLanguage();
  }

  getSupportedLanguages(): LanguageConfig[] {
    return this.SUPPORTED_LANGUAGES;
  }

  resetToDefault(): void {
    const browserLanguage = this.detectBrowserLanguage();
    this.setLanguage(browserLanguage);
  }

  private getStoredLanguage(): SupportedLanguage {
    // First check if user has manually selected a language
    const stored = localStorage.getItem(this.STORAGE_KEY);
    if (stored && this.isSupportedLanguage(stored)) {
      return stored as SupportedLanguage;
    }

    // If no stored preference, detect browser language
    const browserLanguage = this.detectBrowserLanguage();
    if (browserLanguage) {
      return browserLanguage;
    }

    // Fallback to default language
    return this.DEFAULT_LANGUAGE;
  }

  // Method to check if user has explicitly set a language preference
  hasUserSelectedLanguage(): boolean {
    const stored = localStorage.getItem(this.STORAGE_KEY);
    return stored && this.isSupportedLanguage(stored);
  }

  // Method to get language for display (browser language if no user preference)
  getDisplayLanguage(): SupportedLanguage {
    // If user has selected a language, use that
    if (this.hasUserSelectedLanguage()) {
      return this.getCurrentLanguage();
    }

    // Otherwise, use browser language or default
    const browserLanguage = this.detectBrowserLanguage();
    return browserLanguage || this.DEFAULT_LANGUAGE;
  }

  detectBrowserLanguage(): SupportedLanguage | null {
    // Get browser languages in order of preference
    const browserLanguages = navigator.languages || [navigator.language];

    for (const browserLang of browserLanguages) {
      // Check for exact match
      const exactMatch = this.SUPPORTED_LANGUAGES.find(lang =>
        lang.code === browserLang.toLowerCase()
      );
      if (exactMatch) {
        return exactMatch.code;
      }

      // Check for language code match (e.g., 'en-US' -> 'en')
      const langCode = browserLang.split('-')[0].toLowerCase();
      const langMatch = this.SUPPORTED_LANGUAGES.find(lang =>
        lang.code === langCode
      );
      if (langMatch) {
        return langMatch.code;
      }
    }

    return null;
  }

  private isSupportedLanguage(language: string): language is SupportedLanguage {
    return this.SUPPORTED_LANGUAGES.some(lang => lang.code === language);
  }
}
