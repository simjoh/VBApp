import { Injectable } from '@angular/core';
import {TrackService} from "../../track-service";
import {EventService} from "../../../admin/event-admin/event.service";
import {combineAll, map, mergeMap, take} from "rxjs/operators";
import {BehaviorSubject, combineLatest, Observable, Subject} from "rxjs";
import {SelectItem, SelectItemGroup} from "primeng/api";

@Injectable()
export class TracksForEventComponentService {

  reload = new BehaviorSubject(false);

  $tracksforEvent = combineLatest([this.trackService.getAllTracks(), this.eventService.getAllEvents(), this.reload.asObservable()]).pipe(
    take(1),
    mergeMap(([tracks, events, reload]) => {
      const s = []
      const sa = [];
      events.forEach((ev) => {
        const track = []
        const machingtrack = tracks.filter(c => c.event_uid === ev.event_uid)
        machingtrack.map((cur) => {
          track.push( {label: cur.title + " " + cur.start_date_time.slice(0, cur.start_date_time.lastIndexOf(' ')), value: cur.track_uid})
        })
        sa.push({
          label: ev.title, value: ev.event_uid,
          items: track as Array<SelectItem>
        })
      })
      s.push(sa)
      return s;
    })
  ) as Observable<Array<SelectItemGroup>>;

  constructor(private trackService: TrackService, private eventService: EventService) { }



  public trigger(){
    if(this.reload.value === true){
      this.reload.next(false);
    } else {
      this.reload.next(true);
    }
  }

  public currentTrack(track: string){

    this.trackService.currentTrack(track);
  }

}
