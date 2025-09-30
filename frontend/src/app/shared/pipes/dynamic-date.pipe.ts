import { Pipe, PipeTransform } from '@angular/core';
import { DatePipe } from '@angular/common';
import { LanguageService } from '../../core/services/language.service';

@Pipe({
  name: 'dynamicDate',
  pure: false // Make it impure so it responds to language changes
})
export class DynamicDatePipe implements PipeTransform {

  constructor(private languageService: LanguageService) {}

  transform(value: any, format?: string): string | null {
    if (!value) return null;

    // Get current language and determine locale
    const currentLang = this.languageService.getCurrentLanguage();
    const locale = currentLang === 'sv' ? 'sv-SE' : 'en-US';

    // Create a new DatePipe with the current locale
    const datePipe = new DatePipe(locale);
    return datePipe.transform(value, format);
  }
}
