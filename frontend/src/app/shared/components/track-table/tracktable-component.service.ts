import { Injectable } from '@angular/core';
import { TrackRepresentation } from '../../api/api';
import {BehaviorSubject} from "rxjs";
import {TrackService} from "../../track-service";
import {map} from "rxjs/operators";
import {LinkService} from "../../../core/link.service";
import {HttpMethod} from "../../../core/HttpMethod";

@Injectable()
export class TracktableComponentService {

  $tracksSubject = new BehaviorSubject([] as TrackRepresentation[])
  tracks$ = this.$tracksSubject.asObservable().pipe(
    map((resp) => {

      return this.deepCopyProperties(resp);
    })
  );

  constructor(private trackService: TrackService, private linkService: LinkService) { }

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
    try {
      // Check which link is available (only one should exist at a time)
      const hasPublishLink = this.linkService.exists(trackRepresentation.links, 'relation.track.undopublisresults', HttpMethod.PUT);
      const hasUnpublishLink = this.linkService.exists(trackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT);
      console.log('hasPublishLink', hasPublishLink);
      console.log('hasUnpublishLink', hasUnpublishLink);

      // Decide action based on available link
      if (hasPublishLink) {
        // Track is unpublished, we should publish it
        return await this.trackService.publishresult(trackRepresentation);
      } else if (hasUnpublishLink) {
        // Track is published, we should unpublish it
        return await this.trackService.undopublishresult(trackRepresentation);
      } else {
        // No relevant links exist
        throw new Error('No publish or unpublish links available for this track');
      }
    } catch (error) {
      console.error('Error publishing/unpublishing track:', error);
      throw error; // Re-throw so the component can handle it
    }
  }

  deepCopyProperties(obj: any): any {
    // Konverterar till och från JSON, kopierar properties men tappar bort metoder
    return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
  }
}
