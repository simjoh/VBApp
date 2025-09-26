import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

import { SidebarComponent } from './sidebar.component';
import { SidebarService } from './sidebar.service';
import { FlagLanguageSelectorComponent } from '../../shared/components/flag-language-selector/flag-language-selector.component';
import { TranslationPipe } from '../../shared/pipes/translation.pipe';

@NgModule({
  declarations: [
    SidebarComponent
  ],
  imports: [
    CommonModule,
    RouterModule,
    FlagLanguageSelectorComponent,
    TranslationPipe
  ],
  providers: [
    SidebarService
  ],
  exports: [
    SidebarComponent
  ]
})
export class SidebarModule { }
