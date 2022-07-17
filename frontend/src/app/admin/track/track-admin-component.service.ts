import { Injectable } from '@angular/core';
import {EventTrackInformationService} from "../../shared/event-track-information.service";
import {BehaviorSubject, combineLatest} from "rxjs";
import {EventService} from "../shared/service/event.service";
import {EventRepresentation} from "../../shared/api/api";
import {map, mergeMap} from "rxjs/operators";

@Injectable()
export class TrackAdminComponentService {

  $eventTrackSubject = new BehaviorSubject([] as any[]);
  $eventsAndTrack = this.$eventTrackSubject.asObservable().pipe(
    mergeMap((s) => {
     return this.eventtrackService.getEventsAndTracks().pipe(
       map((ss) => {
         return this.sortEvents(ss);
       })
     );
    })
  )

  constructor(private eventtrackService: EventTrackInformationService,
              private eventService: EventService) { }

  async removeEvent(eventtoremove: EventRepresentation){
    await this.eventService.deleterEvent2(eventtoremove.event_uid).then(() => {
      const tracksafterdelete = this.$eventTrackSubject.getValue().filter((eventtrack) => {
        return eventtoremove.event_uid != eventtrack.event.event_uid;
      })
      this.$eventTrackSubject.next(tracksafterdelete);
    })
  }
  deletelinkExists(event: EventRepresentation){
      return this.eventService.deletelinkExists(event);
  }

  init(){
    this.$eventTrackSubject.next([]);
  }

  sortEvents(eventinfo: Array<any>): Array<any>{
    return eventinfo.sort((a, b) => (a.event.startdate > b.event.startdate) ? 1 : -1)
  }

}
