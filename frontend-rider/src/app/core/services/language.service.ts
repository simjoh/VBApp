import { Injectable, signal, computed } from '@angular/core';

export type SupportedLanguage = 'en' | 'sv' | 'fr' | 'de';

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
    { code: 'en', name: 'English', nativeName: 'English', flag: '🇬🇧' },
    { code: 'sv', name: 'Swedish', nativeName: 'Svenska', flag: '🇸🇪' },
    { code: 'fr', name: 'French', nativeName: 'Français', flag: '🇫🇷' },
    { code: 'de', name: 'German', nativeName: 'Deutsch', flag: '🇩🇪' }
  ];

  private readonly DEFAULT_LANGUAGE: SupportedLanguage = 'en';
  private readonly STORAGE_KEY = 'frontend-rider-language';

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
    return [...this.SUPPORTED_LANGUAGES];
  }

  /**
   * Reset language preference to browser default
   * Useful for testing or allowing users to reset their choice
   */
  resetToDefault(): void {
    localStorage.removeItem(this.STORAGE_KEY);
    const browserLanguage = this.detectBrowserLanguage() || this.DEFAULT_LANGUAGE;
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

  detectBrowserLanguage(): SupportedLanguage | null {
    // Get browser languages in order of preference
    const browserLanguages = navigator.languages || [navigator.language];

    for (const browserLang of browserLanguages) {
      // Extract language code (e.g., 'en-US' -> 'en', 'sv-SE' -> 'sv')
      const langCode = browserLang.toLowerCase().split('-')[0];

      // Check if we support this language
      if (this.isSupportedLanguage(langCode)) {
        console.log(`🌍 Detected browser language: ${browserLang} -> ${langCode}`);
        return langCode as SupportedLanguage;
      }
    }

    console.log(`🌍 No supported browser language found in: ${browserLanguages.join(', ')}`);
    return null;
  }

  private isSupportedLanguage(langCode: string): boolean {
    return this.SUPPORTED_LANGUAGES.some(lang => lang.code === langCode);
  }
}
