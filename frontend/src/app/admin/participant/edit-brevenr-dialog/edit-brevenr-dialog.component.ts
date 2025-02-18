import { ChangeDetectionStrategy, Component } from '@angular/core';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";

@Component({
    selector: 'brevet-edit-brevenr-dialog',
    templateUrl: './edit-brevenr-dialog.component.html',
    styleUrls: ['./edit-brevenr-dialog.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class EditBrevenrDialogComponent {



  brevenr;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {
  }

  ngOnInit(): void {
    this.brevenr = this.config.data.brevenr;
  }

  savebrvenr() {
    this.ref.close(this.brevenr);
  }

  cancel() {
    this.ref.close(null);
  }

}
