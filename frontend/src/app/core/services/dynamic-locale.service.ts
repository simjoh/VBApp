import { Injectable, Inject, LOCALE_ID } from '@angular/core';
import { LanguageService } from './language.service';

@Injectable({
  providedIn: 'root'
})
export class DynamicLocaleService {
  constructor(
    @Inject(LOCALE_ID) private localeId: string,
    private languageService: LanguageService
  ) {}

  getCurrentLocale(): string {
    const currentLang = this.languageService.getCurrentLanguage();
    return currentLang === 'sv' ? 'sv-SE' : 'en-US';
  }
}


