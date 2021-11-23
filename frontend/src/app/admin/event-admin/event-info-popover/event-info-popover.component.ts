import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import {EventRepresentation, User} from "../../../shared/api/api";

@Component({
  selector: 'brevet-event-info-popover',
  templateUrl: './event-info-popover.component.html',
  styleUrls: ['./event-info-popover.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EventInfoPopoverComponent implements OnInit {

  @Input() event : EventRepresentation

  constructor() { }

  ngOnInit(): void {
  }

}
