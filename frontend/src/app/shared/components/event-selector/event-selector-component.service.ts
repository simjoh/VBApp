import { Injectable } from '@angular/core';
import {BehaviorSubject} from "rxjs";
import {map} from "rxjs/operators";
import {SelectItem} from "primeng/api";
import {EventService} from "../../../admin/shared/service/event.service";

@Injectable()
export class EventSelectorComponentService {


  reload = new BehaviorSubject(false);


  $eventItems = this.eventService.allEvents$.pipe(
    map((events) => {
      const items: SelectItem[] = [];
      events.map((event) => {
        items.push( { label: event.title,value: event.event_uid})
      });
      return items;
    })
  );

  constructor(private eventService: EventService) { }

  currentEvent($event: any) {

  }

  trigger() {

  }
}
