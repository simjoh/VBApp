import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";

@Component({
    selector: 'brevet-edit-time-dialog',
    templateUrl: './edit-time-dialog.component.html',
    styleUrls: ['./edit-time-dialog.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class EditTimeDialogComponent implements OnInit {


  starttime;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {
  }

  ngOnInit(): void {
    this.starttime = this.config.data.time;
  }

  savetime() {
    this.ref.close(this.starttime);
  }

  cancel() {
    this.ref.close(null);
  }
}
