import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {VolonteerComponentService} from "../volonteer-component.service";
import {map} from "rxjs/operators";
import {SelectItem} from "primeng/api";

@Component({
    selector: 'brevet-event-chooser',
    templateUrl: './event-chooser.component.html',
    styleUrls: ['./event-chooser.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class EventChooserComponent implements OnInit {

  choosenEvent: unknown [] = [0];

  $eventItems = this.volonteerComponentService.$allEvents.pipe(
    map((events) => {
      const items: SelectItem[] = [];
      events.map((event) => {
        items.push( { label: event.title,value: event.event_uid})
      });
      return items;
    })
  );

  constructor(private volonteerComponentService :VolonteerComponentService) { }

  ngOnInit(): void {
  }

  valtEvent() {
    this.volonteerComponentService.valtEvent(this.choosenEvent as unknown as string);
  }
}
