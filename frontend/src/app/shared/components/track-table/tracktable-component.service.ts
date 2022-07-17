import { Injectable } from '@angular/core';
import { TrackRepresentation } from '../../api/api';
import {BehaviorSubject} from "rxjs";
import {TrackService} from "../../track-service";

@Injectable()
export class TracktableComponentService {

  $tracksSubject = new BehaviorSubject([] as TrackRepresentation[])
  tracks$ = this.$tracksSubject.asObservable();

  constructor(private trackService: TrackService) { }

  initiateTracks(tracks: TrackRepresentation[]) {
    this.$tracksSubject.next(tracks)
  }

  linkExists(track: TrackRepresentation): boolean{
      return this.trackService.deletelinkExists(track)
  }

  async remove(tracktoremove: TrackRepresentation){
   await this.trackService.deletetrack(tracktoremove).then(() => {
     const tracksafterdelete = this.$tracksSubject.getValue().filter((track: any) => {
        return track.trackRepresentation.track_uid !=tracktoremove.track_uid;
      })
    })
  }

}
