import { Pipe, PipeTransform, inject } from '@angular/core';
import { TranslationService } from '../../core/services/translation.service';

@Pipe({
  name: 'translatedDate',
  pure: false // Make it impure so it responds to language changes
})
export class TranslatedDatePipe implements PipeTransform {
  private translationService = inject(TranslationService);

  transform(value: any, format?: string): string | null {
    if (!value) return null;

    try {
      const date = new Date(value);
      if (isNaN(date.getTime())) return null;

      if (format === 'd/M/yyyy, HH:mm') {
        // Swedish format: 31/12/2024, 14:30
        const day = this.addZero(date.getDate());
        const month = this.addZero(date.getMonth() + 1);
        const year = date.getFullYear();
        const hours = this.addZero(date.getHours());
        const minutes = this.addZero(date.getMinutes());
        return `${day}/${month}/${year}, ${hours}:${minutes}`;
      }

      // Default format - use Swedish format
      const day = this.addZero(date.getDate());
      const month = this.addZero(date.getMonth() + 1);
      const year = date.getFullYear();
      const hours = this.addZero(date.getHours());
      const minutes = this.addZero(date.getMinutes());
      return `${day}/${month}/${year}, ${hours}:${minutes}`;
    } catch (e) {
      console.error('Error formatting date:', e);
      return null;
    }
  }

  private addZero(i: number): string {
    return i < 10 ? "0" + i : i.toString();
  }
}
