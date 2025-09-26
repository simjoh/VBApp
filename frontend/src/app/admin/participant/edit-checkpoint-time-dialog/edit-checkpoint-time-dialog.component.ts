import { ChangeDetectionStrategy, Component, OnInit, ViewChild, inject } from '@angular/core';
import { DynamicDialogConfig, DynamicDialogRef } from "primeng/dynamicdialog";
import { Calendar } from 'primeng/calendar';
import { TranslationService } from '../../../core/services/translation.service';

@Component({
  selector: 'brevet-edit-checkpoint-time-dialog',
  templateUrl: './edit-checkpoint-time-dialog.component.html',
  styleUrls: ['./edit-checkpoint-time-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EditCheckpointTimeDialogComponent implements OnInit {
  private translationService = inject(TranslationService);

  checkpointTime: Date;
  checkpointAddress: string = '';
  checkpointPlace: string = '';
  isCheckout: boolean = false;

  @ViewChild(Calendar) calendar: Calendar;

  get headerText(): string {
    return this.isCheckout ? this.translationService.translate('checkpointDialog.changeCheckoutTime') : this.translationService.translate('checkpointDialog.changeCheckinTime');
  }

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {}

  ngOnInit(): void {
    this.checkpointTime = this.config.data.time ? new Date(this.config.data.time) : new Date();
    this.checkpointAddress = this.config.data.address || '';
    this.checkpointPlace = this.config.data.place || '';
    this.isCheckout = this.config.data.isCheckout || false;
  }

  saveTime(): void {
    if (this.checkpointTime) {
      this.ref.close(this.checkpointTime);
    }
  }
}
