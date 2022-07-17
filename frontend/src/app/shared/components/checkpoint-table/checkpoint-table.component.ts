import {Component, OnInit, ChangeDetectionStrategy, OnChanges, Input, SimpleChanges} from '@angular/core';
import {CheckpointRepresentation} from "../../api/api";
import {CheckpointTableComponentService} from "./checkpoint-table-component.service";

@Component({
  selector: 'brevet-checkpoint-table',
  templateUrl: './checkpoint-table.component.html',
  styleUrls: ['./checkpoint-table.component.scss'],
  providers: [CheckpointTableComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CheckpointTableComponent implements OnInit, OnChanges {

  $checkpointsviewinformation = this.checkpointcomponentService.checkpoints$;
  @Input() checkpoints: CheckpointRepresentation[];

  constructor(private checkpointcomponentService: CheckpointTableComponentService) { }

  ngOnInit(): void {
    this.checkpointcomponentService.initiateCheckpoints(this.checkpoints);
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes && changes.checkpoints) {
      this.checkpoints = changes.checkpoints.currentValue;
      this.checkpointcomponentService.initiateCheckpoints(this.checkpoints);
    }
  }

}
