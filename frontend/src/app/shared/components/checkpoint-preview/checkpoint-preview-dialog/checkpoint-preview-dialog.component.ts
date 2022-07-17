import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService, DynamicDialogConfig, DynamicDialogRef} from 'primeng/dynamicdialog';
import {CompetitorService} from "../../../../competitor/competitor.service";
import {map} from "rxjs/operators";
import {config} from "rxjs";

@Component({
  selector: 'brevet-checkpoint-preview-dialog',
  templateUrl: './checkpoint-preview-dialog.component.html',
  styleUrls: ['./checkpoint-preview-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CheckpointPreviewDialogComponent implements OnInit {

  track_uid: string
  previewdata$

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig, private competitorService: CompetitorService) { }

  ngOnInit(): void {

    this.track_uid = this.config.data.track;
     this.previewdata$ =   this.competitorService.getCheckpointsPreview(this.config.data.track).pipe(
       map((data) => {
         return data;
       })
     )

  }

}
