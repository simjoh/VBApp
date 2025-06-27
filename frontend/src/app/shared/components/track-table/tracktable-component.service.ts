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
      // Check which links are available
      const hasPublishLink = this.trackService.publishReultLinkExists(trackRepresentation);
      const hasUnpublishLink = this.linkService.exists(trackRepresentation.links, 'relation.track.undopublisresults', HttpMethod.PUT);

      // Decide action based on available links
      if (hasPublishLink && !hasUnpublishLink) {
        // Track is inactive (unpublished), we should publish it
        return await this.trackService.publishresult(trackRepresentation);
      } else if (!hasPublishLink && hasUnpublishLink) {
        // Track is active (published), we should unpublish it
        return await this.trackService.undopublishresult(trackRepresentation);
      } else if (hasPublishLink && hasUnpublishLink) {
        // Both links exist - this shouldn't happen, but let's handle it
        console.error('Both publish and unpublish links exist - inconsistent state');
        // Default to publish since we have a publish link
        return await this.trackService.publishresult(trackRepresentation);
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
