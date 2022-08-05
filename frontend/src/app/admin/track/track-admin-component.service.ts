import { Injectable } from '@angular/core';
import {EventTrackInformationService} from "../../shared/event-track-information.service";
import {BehaviorSubject, combineLatest, Observable, pipe, Subject} from "rxjs";
import {EventService} from "../shared/service/event.service";
import {
  EventInformationRepresentation,
  EventRepresentation,
  ParticipantInformationRepresentation,
  TrackRepresentation
} from "../../shared/api/api";
import {map, mergeMap, startWith} from "rxjs/operators";
import { TrackService } from 'src/app/shared/track-service';

@Injectable()
export class TrackAdminComponentService {


  $eventTrackSubject = new BehaviorSubject([] as any[]);
  // $eventsAndTrack = this.$eventTrackSubject.asObservable().pipe(
  //   mergeMap((s) => {
  //    return this.eventtrackService.getEventsAndTracks().pipe(
  //      map((ss) => {
  //        return this.sortEvents(ss);
  //      })
  //    );
  //   })
  // )


  $reloadSubject = new Subject();
  $reload = this.$reloadSubject.asObservable();


  $eventsAndTrack = combineLatest(([this.$eventTrackSubject.asObservable().pipe(startWith([])), this.eventtrackService.getEventsAndTracks()])).pipe(
    mergeMap(([checkin ,part]) => {
         return this.eventtrackService.getEventsAndTracks().pipe(
           map((ss) => {
             return this.sortEvents(ss);
           })
         );
    })
  ) as Observable<EventInformationRepresentation[]>;



  constructor(private eventtrackService: EventTrackInformationService,
              private eventService: EventService, private trackservice: TrackService) { }

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

  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }

}
