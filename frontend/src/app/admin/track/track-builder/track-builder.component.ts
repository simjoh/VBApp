import { Component } from '@angular/core';
import { TrackBuilderComponentService } from './track-builder-component.service';
import { map } from 'rxjs/operators';
import { CommonModule } from '@angular/common';
import { ButtonModule } from 'primeng/button';
import { TrackBuilderSummaryComponent } from './track-builder-summary/track-builder-summary.component';
import { TrackBuilderTrackInfoFormComponent } from './track-builder-track-info-form/track-builder-track-info-form.component';
import { TrackBuilderControlsFormComponent } from './track-builder-controls-form/track-builder-controls-form.component';

@Component({
  selector: 'brevet-track-builder',
  templateUrl: './track-builder.component.html',
  styleUrls: ['./track-builder.component.scss'],
  standalone: true,
  imports: [
    CommonModule,
    ButtonModule,
    TrackBuilderSummaryComponent,
    TrackBuilderTrackInfoFormComponent,
    TrackBuilderControlsFormComponent
  ],
  providers: [TrackBuilderComponentService]
})
export class TrackBuilderComponent {
  editMode$ = this.trackbuildercomponentService.$rusaPlannerInput.pipe(
    map(input => {
      return Object.keys(input).length > 0;
    })
  );

  constructor(private trackbuildercomponentService: TrackBuilderComponentService) { }

  setMode() {
    this.trackbuildercomponentService.rusaInput({
      event_distance: 0,
      start_time: "",
      start_date: "",
      event_uid: "",
      track_title: "",
      controls: [],
      link: ""
    });
  }
}
