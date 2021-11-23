import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {map} from "rxjs/operators";
import {EventRepresentation, Site} from "../../../shared/api/api";
import {Observable} from "rxjs";
import {EventService} from "../event.service";

@Component({
  selector: 'brevet-event-list',
  templateUrl: './event-list.component.html',
  styleUrls: ['./event-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EventListComponent implements OnInit {


  events$ = this.eventService.allEvents$.pipe(
    map((s:Array<EventRepresentation>) => {
      // this.table. = 0;
      return s;
    })
  ) as Observable<EventRepresentation[]>;

  constructor(private eventService: EventService) { }

  ngOnInit(): void {
  }

  openNew() {

  }

  editProduct(user_uid: any) {

  }

  deleteProduct(user_uid: any) {

  }
}
