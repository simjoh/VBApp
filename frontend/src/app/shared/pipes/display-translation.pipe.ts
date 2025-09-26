import { Pipe, PipeTransform, inject } from '@angular/core';
import { TranslationService } from '../../core/services/translation.service';

@Pipe({
  name: 'displayTranslate',
  standalone: true,
  pure: false
})
export class DisplayTranslationPipe implements PipeTransform {
  private translationService = inject(TranslationService);

  transform(key: string): string {
    return this.translationService.translateForDisplay(key as any);
  }
}

