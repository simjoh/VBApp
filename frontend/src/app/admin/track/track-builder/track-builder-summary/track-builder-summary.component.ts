import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef, OnDestroy } from '@angular/core';
import {TrackBuilderComponentService} from "../track-builder-component.service";
import {map, catchError, take} from "rxjs/operators";
import {combineLatest, of, Subscription} from "rxjs";

@Component({
  selector: 'brevet-track-builder-summary',
  templateUrl: './track-builder-summary.component.html',
  styleUrls: ['./track-builder-summary.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackBuilderSummaryComponent implements OnInit, OnDestroy {
  // Track subscriptions
  private subscriptions: Subscription[] = [];

  // Loading state
  isLoading = false;

  // Data
  event: any = null;
  track: any = null;
  controls: any[] = [];
  buttonDisabled = true;

  constructor(
    private trackbuildercomponentService: TrackBuilderComponentService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
    // Subscribe to the event
    this.subscriptions.push(
      this.trackbuildercomponentService.$currentEvent.subscribe(event => {
        this.event = event;
        this.cdr.markForCheck();
      })
    );

    // Subscribe to the track data
    this.subscriptions.push(
      this.trackbuildercomponentService.$all.subscribe(data => {
        if (data) {
          // Update track data
          if (data.rusaTrackRepresentation) {
            this.track = data.rusaTrackRepresentation;
          }

          // Update controls data - always sort by distance
          if (data.rusaplannercontrols) {
            // Create deep copies to avoid reference issues
            const controlsCopy = data.rusaplannercontrols.map(control => ({...control}));

            // Sort controls by distance and update the view
            this.controls = this.sortControlsByDistance(controlsCopy);
          }

          // Update button state
          this.updateButtonState();

          // Update the UI
          this.cdr.markForCheck();
        }
      })
    );
  }

  ngOnDestroy(): void {
    // Clean up subscriptions
    this.subscriptions.forEach(sub => sub.unsubscribe());
  }

  private updateButtonState(): void {
    // Check if the button should be enabled
    if (this.track && this.controls && this.controls.length > 0) {
      // Enable the button if at least one control has a distance >= track distance
      this.buttonDisabled = !this.controls.some(control => {
        const controlDistance = control.rusaControlRepresentation ?
          control.rusaControlRepresentation.CONTROL_DISTANCE_KM :
          (control.DISTANCE || 0);

        return controlDistance >= this.track.EVENT_DISTANCE_KM;
      });
    } else {
      this.buttonDisabled = true;
    }
  }

  /**
   * Sorts controls by distance in ascending order
   * This ensures the summary always shows controls in the correct order
   * regardless of the order they were added in the form
   */
  private sortControlsByDistance(controls: any[]): any[] {
    if (!controls || controls.length <= 1) {
      return controls;
    }

    // Sort the controls by distance
    return controls.sort((a, b) => {
      // Get distance from rusaControlRepresentation if available, otherwise use DISTANCE
      const distanceA = a.rusaControlRepresentation ?
        a.rusaControlRepresentation.CONTROL_DISTANCE_KM :
        (a.DISTANCE || 0);

      const distanceB = b.rusaControlRepresentation ?
        b.rusaControlRepresentation.CONTROL_DISTANCE_KM :
        (b.DISTANCE || 0);

      // Sort in ascending order
      return distanceA - distanceB;
    });
  }

  createTrack(): void {
    // Set loading state
    this.isLoading = true;
    this.cdr.markForCheck();

    // Create the track
    this.trackbuildercomponentService.createTrack()
      .then(() => {
        // Reset loading state
        this.isLoading = false;
        this.cdr.markForCheck();
      })
      .catch(() => {
        // Reset loading state on error
        this.isLoading = false;
        this.cdr.markForCheck();
      });
  }
}
