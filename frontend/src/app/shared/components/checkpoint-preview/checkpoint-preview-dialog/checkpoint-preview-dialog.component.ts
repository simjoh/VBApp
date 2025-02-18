import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {DynamicDialogConfig, DynamicDialogRef} from 'primeng/dynamicdialog';
import {CompetitorService} from "../../../../competitor/competitor.service";
import {map} from "rxjs/operators";
import {RandonneurCheckPointRepresentation} from "../../../api/api";

@Component({
    selector: 'brevet-checkpoint-preview-dialog',
    templateUrl: './checkpoint-preview-dialog.component.html',
    styleUrls: ['./checkpoint-preview-dialog.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class CheckpointPreviewDialogComponent implements OnInit {

  track_uid: string
  previewdata$

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig, private competitorService: CompetitorService) {
  }

  ngOnInit(): void {

    this.track_uid = this.config.data.track;
    this.previewdata$ = this.competitorService.getCheckpointsPreview(this.config.data.track).pipe(
      map((data) => {
        return data;
      })
    )

  }

  nextISSceret(s: RandonneurCheckPointRepresentation) {

    if (!s) {
      return false;
    }
    if (s.checkpoint.site.adress === '-' || s.checkpoint.site.place.toLowerCase() === 'secret' || s.checkpoint.site.adress.toLowerCase() === 'hemlig') {
      return true;
    }
    return false;
  }
}
