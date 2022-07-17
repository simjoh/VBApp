import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import {DialogService, DynamicDialogRef} from "primeng/dynamicdialog";
import {CheckpointPreviewDialogComponent} from "./checkpoint-preview-dialog/checkpoint-preview-dialog.component";
import {ConfirmationService} from "primeng/api";
import {CompetitorService} from "../../../competitor/competitor.service";

@Component({
  selector: 'brevet-checkpoint-preview',
  templateUrl: './checkpoint-preview.component.html',
  styleUrls: ['./checkpoint-preview.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CheckpointPreviewComponent implements OnInit {


  ref: DynamicDialogRef;

  @Input() track_uid: string

  constructor(public dialogService: DialogService) { }

  ngOnInit(): void {
  }

  showPreview() {
    this.ref = this.dialogService.open(CheckpointPreviewDialogComponent, {
      header: 'Cyklistens vy',
      width: '25%',
      data: {track: this.track_uid},
      contentStyle: {"max-height": "800px", "overflow": "auto"},
      baseZIndex: 10000
    });

    this.ref.onClose.subscribe((product: any) =>{
      if (product) {
           console.log("ssssssssss")
      }
    });
  }
}
