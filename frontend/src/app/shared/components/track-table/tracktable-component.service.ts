import { Injectable } from '@angular/core';
import { TrackRepresentation } from '../../api/api';
import {BehaviorSubject} from "rxjs";
import {TrackService} from "../../track-service";
import {map} from "rxjs/operators";

@Injectable()
export class TracktableComponentService {

  $tracksSubject = new BehaviorSubject([] as TrackRepresentation[])
  tracks$ = this.$tracksSubject.asObservable().pipe(
    map((resp) => {

      return this.deepCopyProperties(resp);
    })
  );

  constructor(private trackService: TrackService) { }

  initiateTracks(tracks: TrackRepresentation[]) {
    this.$tracksSubject.next(tracks)
  }

  linkExists(track: TrackRepresentation): boolean{
      return this.trackService.deletelinkExists(track)
  }

publishReultLinkExists(track: TrackRepresentation){
   return this.trackService.publishReultLinkExists(track)
  }


  async remove(tracktoremove: TrackRepresentation){
   await this.trackService.deletetrack(tracktoremove).then(() => {
     const tracksafterdelete = this.$tracksSubject.getValue().filter((track: any) => {
        return track.trackRepresentation.track_uid !=tracktoremove.track_uid;
      })
    })
  }

  async publishResults(trackRepresentation: TrackRepresentation) {
    if (this.publishReultLinkExists(trackRepresentation) === true){
      return  await this.trackService.publishresult(trackRepresentation);
    } else {
      return  await this.trackService.undopublishresult(trackRepresentation);
    }

  }

  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }
}
