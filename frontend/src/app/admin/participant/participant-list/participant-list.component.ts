import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import {ParticipantComponentService} from "../participant-component.service";
import {TrackService} from '../../../shared/track-service';

@Component({
  selector: 'brevet-participant-list',
  templateUrl: './participant-list.component.html',
  styleUrls: ['./participant-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantListComponent implements OnInit {

  // $track = this.participantComponentService.$currentTrack

  constructor(
    private participantComponentService: ParticipantComponentService,
    private route: ActivatedRoute,
    private trackService: TrackService
  ) { }

  ngOnInit(): void {
    // Check for track query parameter
    this.route.queryParams.subscribe(params => {
      if (params['track']) {
        // Set the track in the track service (this is what the participant service listens to)
        this.trackService.currentTrack(params['track']);
      } else {
        // Reset to no track if no query parameter
        this.trackService.currentTrack(null);
      }
    });
  }

}
