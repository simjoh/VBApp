import { Pipe, PipeTransform, inject } from '@angular/core';
import { TranslationService } from '../../core/services/translation.service';

@Pipe({
  name: 'translate',
  standalone: true,
  pure: false
})
export class TranslationPipe implements PipeTransform {
  private translationService = inject(TranslationService);

  transform(key: string): string {
    return this.translationService.translate(key as any);
  }
}

