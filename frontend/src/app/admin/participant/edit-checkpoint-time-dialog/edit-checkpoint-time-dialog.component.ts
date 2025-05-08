import { ChangeDetectionStrategy, Component, OnInit, ViewChild } from '@angular/core';
import { DynamicDialogConfig, DynamicDialogRef } from "primeng/dynamicdialog";
import { Calendar } from 'primeng/calendar';

@Component({
  selector: 'brevet-edit-checkpoint-time-dialog',
  templateUrl: './edit-checkpoint-time-dialog.component.html',
  styleUrls: ['./edit-checkpoint-time-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EditCheckpointTimeDialogComponent implements OnInit {
  checkpointTime: Date;
  checkpointAddress: string = '';
  checkpointPlace: string = '';
  isCheckout: boolean = false;

  @ViewChild(Calendar) calendar: Calendar;

  get headerText(): string {
    return this.isCheckout ? 'Ändra utcheckningtid' : 'Ändra incheckningstid';
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
