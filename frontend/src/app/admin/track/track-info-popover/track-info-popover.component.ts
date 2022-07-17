import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import {TrackMetricsRepresentation} from "../../../shared/api/api";

@Component({
  selector: 'brevet-track-info-popover',
  templateUrl: './track-info-popover.component.html',
  styleUrls: ['./track-info-popover.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackInfoPopoverComponent implements OnInit {


  @Input() trackmetrics : TrackMetricsRepresentation;

  constructor() { }

  ngOnInit(): void {
  }

}
