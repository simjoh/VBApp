import {Component, OnInit, ChangeDetectionStrategy, Output, ChangeDetectorRef, OnDestroy} from '@angular/core';
import {RusaPlannerControlInputRepresentation} from "../../../../shared/api/rusaTimeApi";
import {SiteRepresentation} from "../../../../shared/api/api";
import {TrackBuilderComponentService} from "../track-builder-component.service";

@Component({
  selector: 'brevet-track-builder-controls-form',
  templateUrl: './track-builder-controls-form.component.html',
  styleUrls: ['./track-builder-controls-form.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class TrackBuilderControlsFormComponent implements OnInit, OnDestroy {

  rusatimeControls: Array<RusaPlannerControlInputRepresentation> = [];

  // Timer for debouncing updates
  private updateTimer: any = null;

  // Track which control is being updated
  private updatingControlIndex: number | null = null;

  constructor(
    private trackbuildercomponentService: TrackBuilderComponentService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
    // Initial empty state
  }

  ngOnDestroy(): void {
    // Clear any pending timers
    if (this.updateTimer) {
      clearTimeout(this.updateTimer);
    }
  }

  addControl() {
    // Add a new control with default values
    const newControl = {
      DISTANCE: 0,
      SITE: ""
    };

    this.rusatimeControls.push(newControl);

    // Update the UI
    this.cdr.markForCheck();

    // Send all controls to the service
    this.debouncedUpdateAll();
  }

  removeControl(index: number) {
    // Check if the index is valid
    if (index >= 0 && index < this.rusatimeControls.length) {
      // Remove the control
      this.rusatimeControls.splice(index, 1);

      // Update the UI
      this.cdr.markForCheck();

      // Send all controls to the service
      this.debouncedUpdateAll();
    }
  }

  addSite($event: any, i: number) {
    // Check if the index is valid
    if (i >= 0 && i < this.rusatimeControls.length) {
      // Update the site
      this.rusatimeControls[i].SITE = $event;

      // Update the UI
      this.cdr.markForCheck();

      // Update just this control
      this.debouncedUpdateSingle(i);
    }
  }

  publish($event: any, index?: number) {
    // Prevent default behavior
    if ($event && $event.preventDefault) {
      $event.preventDefault();
    }

    // Check if the index is valid
    if (index !== undefined && index >= 0 && index < this.rusatimeControls.length) {
      // Update the distance, ensuring it's a number
      this.rusatimeControls[index].DISTANCE = $event === null ? 0 : $event;

      // Update the UI
      this.cdr.markForCheck();

      // Update just this control
      this.debouncedUpdateSingle(index);
    }
  }

  private debouncedUpdateSingle(index: number) {
    // Clear any existing timer
    if (this.updateTimer) {
      clearTimeout(this.updateTimer);
    }

    // Save the index of the control being updated
    this.updatingControlIndex = index;

    // Set a new timer to update after a delay
    this.updateTimer = setTimeout(() => {
      this.updateSingleControl();
    }, 300);
  }

  private debouncedUpdateAll() {
    // Clear any existing timer
    if (this.updateTimer) {
      clearTimeout(this.updateTimer);
    }

    // Reset the updating control index
    this.updatingControlIndex = null;

    // Set a new timer to update after a delay
    this.updateTimer = setTimeout(() => {
      this.updateAllControls();
    }, 300);
  }

  private updateSingleControl() {
    // Check if we have a valid index
    if (this.updatingControlIndex !== null &&
        this.updatingControlIndex >= 0 &&
        this.updatingControlIndex < this.rusatimeControls.length) {

      // Get the control to update
      const control = this.rusatimeControls[this.updatingControlIndex];

      // Create a clean copy of the control
      const cleanControl = {
        DISTANCE: control.DISTANCE === null ? 0 : control.DISTANCE,
        SITE: control.SITE || ""
      };

      // Send just this control to the service
      this.trackbuildercomponentService.updateSingleControl(
        cleanControl,
        this.updatingControlIndex
      );

      // Reset the updating control index
      this.updatingControlIndex = null;
    }
  }

  private updateAllControls() {
    // Create a clean copy of all controls
    const cleanControls = this.rusatimeControls.map(control => ({
      DISTANCE: control.DISTANCE === null ? 0 : control.DISTANCE,
      SITE: control.SITE || ""
    }));

    // Send all controls to the service
    this.trackbuildercomponentService.addControls(cleanControls);
  }
}

