import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { RusaPlannerControlInputRepresentation } from "../../../../shared/api/rusaTimeApi";
import { SiteRepresentation } from "../../../../shared/api/api";
import { TrackBuilderComponentService } from "../track-builder-component.service";
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CardModule } from 'primeng/card';
import { ButtonModule } from 'primeng/button';
import { InputNumberModule } from 'primeng/inputnumber';
import { TooltipModule } from 'primeng/tooltip';
import { DropdownModule } from 'primeng/dropdown';
import { SharedModule } from '../../../../shared/shared.module';

@Component({
  selector: 'brevet-track-builder-controls-form',
  templateUrl: './track-builder-controls-form.component.html',
  styleUrls: ['./track-builder-controls-form.component.scss'],
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    CardModule,
    ButtonModule,
    InputNumberModule,
    TooltipModule,
    DropdownModule,
    SharedModule
  ]
})
export class TrackBuilderControlsFormComponent implements OnInit {
  rusatimeControls: Array<RusaPlannerControlInputRepresentation> = [];
  trackSaved$ = this.trackbuildercomponentService.trackSaved$;

  constructor(private trackbuildercomponentService: TrackBuilderComponentService) { }

  ngOnInit(): void {
    // Initialize with empty array
    this.rusatimeControls = [];
  }

  addControl() {
    const newControl = this.emptyControlObject();
    this.rusatimeControls.push(newControl);
    this.sortControls();
    this.publishControls(); // Publish updated controls
  }

  private emptyControlObject(): RusaPlannerControlInputRepresentation {
    return {
      DISTANCE: null,
      SITE: "",
    } as RusaPlannerControlInputRepresentation;
  }

  addSite($event: any, i: number) {
    this.rusatimeControls[i].SITE = $event;
    this.publishControls(); // Publish when site is updated
  }

  updateDistance(index: number) {
    this.sortControls(); // Sort controls when distance is updated
    this.publishControls(); // Publish when distance is updated
  }

  removeControl(index: number) {
    this.rusatimeControls.splice(index, 1);
    this.publishControls();
  }

  private sortControls() {
    this.rusatimeControls.sort((a, b) => {
      // Handle null/undefined distances
      if (a.DISTANCE === null || a.DISTANCE === undefined) return 1;
      if (b.DISTANCE === null || b.DISTANCE === undefined) return -1;
      return a.DISTANCE - b.DISTANCE;
    });
  }

  private publishControls() {
    // Only publish if we have valid controls
    const validControls = this.rusatimeControls.filter(control =>
      control.SITE && control.DISTANCE !== null
    );
    if (validControls.length > 0) {
      // Make sure controls are sorted before publishing
      validControls.sort((a, b) => a.DISTANCE - b.DISTANCE);
      this.trackbuildercomponentService.addControls([...validControls]);
    }
  }
}
