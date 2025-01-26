import {Component, OnInit, ChangeDetectionStrategy, Input, Output, EventEmitter} from '@angular/core';
import { SelectItemGroup } from 'primeng/api';
import {TracksForEventComponentService} from "./tracks-for-event-component.service";
import {map} from "rxjs/operators";
import {Observable} from "rxjs";

@Component({
  selector: 'brevet-tracks-for-event-selector',
  templateUrl: './tracks-for-event-selector.component.html',
  styleUrls: ['./tracks-for-event-selector.component.scss'],
  providers: [TracksForEventComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TracksForEventSelectorComponent implements OnInit {

  @Input() filter: boolean;
  @Input() showclear: boolean;
  @Input() placeholder: string;
  @Input() styleClass: string;
  @Output() trackChange: EventEmitter<any> = new EventEmitter();

  selectedTrack: string;
  selected: [];
  $items = this.trackForEventComponentService.$tracksforEvent

  constructor(private trackForEventComponentService: TracksForEventComponentService) { }

  ngOnInit(): void {
    this.trackForEventComponentService.trigger();
  }

  setValue($event: any) {
   // this.trackChange.emit(this.selectedTrack);
    if($event){
      this.trackChange.emit($event);
      this.trackForEventComponentService.currentTrack($event);
    }

  }
}
