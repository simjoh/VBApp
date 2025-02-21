import { Component } from '@angular/core';
import { map, take } from 'rxjs/operators';
import { firstValueFrom } from 'rxjs';
import { TrackBuilderComponentService } from '../track-builder-component.service';
import { RusaPlannerControlInputRepresentation, RusaPlannerResponseRepresentation } from 'src/app/shared/api/rusaTimeApi';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { CardModule } from 'primeng/card';
import { ButtonModule } from 'primeng/button';
import { ImageModule } from 'primeng/image';
import { SharedModule } from '../../../../shared/shared.module';

@Component({
  selector: 'brevet-track-builder-summary',
  templateUrl: './track-builder-summary.component.html',
  styleUrls: ['./track-builder-summary.component.scss'],
  standalone: true,
  imports: [
    CommonModule,
    CardModule,
    ButtonModule,
    ImageModule,
    SharedModule
  ]
})
export class TrackBuilderSummaryComponent {
  saving = false;

  event$ = this.trackbuildercomponentService.$currentEvent;
  $track = this.trackbuildercomponentService.$rusaPlannerInput;
  trackSaved$ = this.trackbuildercomponentService.trackSaved$;
  $summary = this.trackbuildercomponentService.$summary;

  // Use summary for display
  $controls = this.$summary.pipe(
    map((summary: RusaPlannerResponseRepresentation | null) => {
      if (!summary) return [];
      return summary.rusaplannercontrols;
    })
  );

  // Button disable logic using rusaPlannerControlsInput
  $buttonDisable = this.trackbuildercomponentService.$rusaPlannerControlsInput.pipe(
    map(controls => !controls || controls.length === 0)
  );

  constructor(
    private trackbuildercomponentService: TrackBuilderComponentService,
    private router: Router
  ) { }

  async saveControls() {
    try {
      this.saving = true;
      const summary = await firstValueFrom(this.$summary);
      if (!summary) {
        console.error('No track data available');
        return;
      }

      const trackId = this.trackbuildercomponentService.getCurrentTrackId();
      if (trackId) {
        // Update existing track
        await firstValueFrom(this.trackbuildercomponentService.saveTrack({
          trackdistance: summary.rusaTrackRepresentation.EVENT_DISTANCE_KM,
          trackname: summary.rusaTrackRepresentation.TRACK_TITLE,
          event_uid: summary.eventRepresentation.event_uid,
          starttime: summary.rusaTrackRepresentation.START_TIME,
          startdate: summary.rusaTrackRepresentation.START_DATE,
          link: summary.rusaTrackRepresentation.LINK_TO_TRACK
        }));
      } else {
        // Create new track
        await firstValueFrom(this.trackbuildercomponentService.saveTrack({
          trackdistance: summary.rusaTrackRepresentation.EVENT_DISTANCE_KM,
          trackname: summary.rusaTrackRepresentation.TRACK_TITLE,
          event_uid: summary.eventRepresentation.event_uid,
          starttime: summary.rusaTrackRepresentation.START_TIME,
          startdate: summary.rusaTrackRepresentation.START_DATE,
          link: summary.rusaTrackRepresentation.LINK_TO_TRACK
        }));
      }

      console.log('Track saved successfully');
      this.router.navigate(['/tracks']);
    } catch (error) {
      console.error('Error saving track:', error);
    } finally {
      this.saving = false;
    }
  }
}
