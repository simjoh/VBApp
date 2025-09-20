import { Pipe, PipeTransform, inject } from '@angular/core';
import { TranslationService } from '../../core/services/translation.service';
import { TranslationKeys } from '../../core/services/translation.service';

@Pipe({
  name: 'translate',
  standalone: true,
  pure: false // Make it impure to react to language changes
})
export class TranslationPipe implements PipeTransform {
  private translationService = inject(TranslationService);

  transform(key: keyof TranslationKeys): string {
    return this.translationService.translate(key);
  }
}
