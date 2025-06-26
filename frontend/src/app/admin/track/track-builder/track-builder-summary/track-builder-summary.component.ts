import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef, OnDestroy } from '@angular/core';
import {TrackBuilderComponentService} from "../track-builder-component.service";
import {OrganizerService, OrganizerRepresentation} from "../../../organizer-admin/organizer.service";
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
  organizer: OrganizerRepresentation | null = null;
  formData: any = {};
  buttonDisabled = true;

  constructor(
    private trackbuildercomponentService: TrackBuilderComponentService,
    private organizerService: OrganizerService,
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

    // Subscribe to the organizer
    this.subscriptions.push(
      this.trackbuildercomponentService.$organizer.subscribe(organizer => {
        this.organizer = organizer;
        this.cdr.markForCheck();
      })
    );

    // Subscribe to form data for real-time updates
    this.subscriptions.push(
      this.trackbuildercomponentService.$formData.subscribe(formData => {
        this.formData = formData;
        this.cdr.markForCheck();
      })
    );

    // Subscribe to the track data
    this.subscriptions.push(
      this.trackbuildercomponentService.$all.subscribe(data => {
        console.log('Summary received $all data:', data);
        if (data) {
          // Update track data
          if (data.rusaTrackRepresentation) {
            this.track = data.rusaTrackRepresentation;
            console.log('Updated track:', this.track);
          }

          // Update controls data - always sort by distance
          if (data.rusaplannercontrols) {
            console.log('Found rusaplannercontrols:', data.rusaplannercontrols);
            // Create deep copies to avoid reference issues
            const controlsCopy = data.rusaplannercontrols.map(control => ({...control}));

            // Sort controls by distance and update the view
            this.controls = this.sortControlsByDistance(controlsCopy);
            console.log('Updated controls:', this.controls);
          } else {
            console.log('No rusaplannercontrols found in data');
          }

          // Update button state
          this.updateButtonState();

          // Update the UI
          this.cdr.markForCheck();
        } else {
          console.log('No data received in $all subscription');
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

    // Event preview methods with placeholder support
  shouldShowEventPreview(): boolean {
    return true; // Always show the preview card
  }

  // Check methods for conditional styling
  hasEventTitle(): boolean {
    return !!(this.formData.trackname || (this.track && this.track.TRACK_TITLE));
  }

  hasDistance(): boolean {
    return !!(this.formData.trackdistance || (this.track && this.track.EVENT_DISTANCE_KM));
  }

  hasElevation(): boolean {
    return !!(this.formData.elevation || this.hasDistance());
  }

  hasStartDate(): boolean {
    return !!(this.formData.startdate || (this.track && this.track.START_DATE));
  }

  hasStartTime(): boolean {
    return !!(this.formData.starttime || (this.track && this.track.START_TIME));
  }

  hasOrganizer(): boolean {
    return !!this.organizer;
  }

  hasDescription(): boolean {
    return !!(this.formData.description || (this.organizer && this.organizer.description));
  }

  hasStartLocation(): boolean {
    return !!this.formData.startLocation;
  }

  hasPayment(): boolean {
    return !!(this.formData.payment || (this.organizer && this.organizer.website_pay));
  }

  // Display methods with placeholders
  getEventTitleWithPlaceholder(): string {
    return this.formData.trackname || this.track?.TRACK_TITLE || 'Arrangemangsnnam';
  }

  getDistanceDisplayWithPlaceholder(): string {
    const distance = this.formData.trackdistance || this.track?.EVENT_DISTANCE_KM;
    return distance ? `${distance} KM` : '200 KM';
  }

  getElevationDisplayWithPlaceholder(): string {
    // First check if we have elevation from form data
    if (this.formData.elevation) {
      return `${this.formData.elevation} M`;
    }

    // Fallback to calculating from distance (existing logic)
    const distance = this.formData.trackdistance || this.track?.EVENT_DISTANCE_KM;
    if (distance) {
      const estimatedElevation = Math.round(distance * 8.5);
      return `${estimatedElevation} M`;
    }
    return '1700 M';
  }

  getStartDateDisplayWithPlaceholder(): string {
    const startDate = this.formData.startdate || this.track?.START_DATE;
    if (startDate) {
      const date = new Date(startDate);
      if (!isNaN(date.getTime())) {
        const day = date.getDate();
        const monthNames = ["Januari", "Februari", "Mars", "April", "Maj", "Juni",
                           "Juli", "Augusti", "September", "Oktober", "November", "December"];
        const month = monthNames[date.getMonth()];
        return `${day} ${month}`;
      }
      return startDate;
    }
    return '15 Juni';
  }

  getStartTimeDisplayWithPlaceholder(): string {
    return this.formData.starttime || this.track?.START_TIME || '07:00';
  }

  getLastRegistrationDisplayWithPlaceholder(): string {
    // First check if we have registration_closes from form data
    if (this.formData.registration_closes) {
      const regClosesDate = new Date(this.formData.registration_closes);
      if (!isNaN(regClosesDate.getTime())) {
        const day = regClosesDate.getDate();
        const monthNames = ["Januari", "Februari", "Mars", "April", "Maj", "Juni",
                           "Juli", "Augusti", "September", "Oktober", "November", "December"];
        const month = monthNames[regClosesDate.getMonth()];
        return `${day} ${month}`;
      }
    }

    // Fallback to calculating from start date (existing logic)
    const startDate = this.formData.startdate || this.track?.START_DATE;
    if (startDate) {
      const date = new Date(startDate);
      if (!isNaN(date.getTime())) {
        const lastReg = new Date(date);
        lastReg.setDate(date.getDate() - 1);
        const day = lastReg.getDate();
        const monthNames = ["Januari", "Februari", "Mars", "April", "Maj", "Juni",
                           "Juli", "Augusti", "September", "Oktober", "November", "December"];
        const month = monthNames[lastReg.getMonth()];
        return `${day} ${month}`;
      }
    }
    return '14 Juni';
  }

  getStartLocationDisplayWithPlaceholder(): string {
    return this.formData.startLocation || 'Startplatsen, Stad';
  }

  // Legacy methods for backward compatibility
  getEventTitle(): string {
    return this.getEventTitleWithPlaceholder();
  }

  getDistanceDisplay(): string {
    return this.hasDistance() ? this.getDistanceDisplayWithPlaceholder() : 'N/A';
  }

  getElevationDisplay(): string {
    return this.hasDistance() ? this.getElevationDisplayWithPlaceholder() : 'N/A';
  }

  getStartDateDisplay(): string {
    return this.hasStartDate() ? this.getStartDateDisplayWithPlaceholder() : '';
  }

  getStartTimeDisplay(): string {
    return this.getStartTimeDisplayWithPlaceholder();
  }

  getLastRegistrationDisplay(): string {
    return this.hasStartDate() ? this.getLastRegistrationDisplayWithPlaceholder() : '';
  }

  getStartLocationDisplay(): string {
    return this.getStartLocationDisplayWithPlaceholder();
  }

  getOrganizerDisplayWithPlaceholder(): string {
    if (this.organizer) {
      return this.organizer.organization_name || this.organizer.contact_person_name || 'Vald arrangör';
    }
    return 'Cykelklubben';
  }

  getPaymentDisplayWithPlaceholder(): string {
    if (this.formData.payment) {
      return this.formData.payment;
    }
    if (this.organizer && this.organizer.website_pay) {
      return this.organizer.website_pay;
    }
    return 'Swish 123 456 78 90';
  }

  getDescriptionWithPlaceholder(): string {
    if (this.formData.description) {
      return this.formData.description;
    }
    if (this.organizer && this.organizer.description) {
      return this.organizer.description;
    }
    return 'Kort beskrivning av eventet';
  }

  // Legacy methods for backward compatibility
  getOrganizerDisplay(): string {
    if (this.organizer) {
      return this.organizer.organization_name || this.organizer.contact_person_name || 'Vald arrangör';
    }
    return 'Ingen arrangör vald';
  }

  getOrganizerLogo(): string {
    if (this.organizer && this.organizer.logo_svg) {
      return this.getSafeLogoUrl(this.organizer.logo_svg);
    }
    return '';
  }

  getOrganizerWebsite(): string {
    if (this.organizer && this.organizer.website) {
      return this.organizer.website;
    }
    return '';
  }

  getPaymentDisplay(): string {
    if (this.organizer && this.organizer.website_pay) {
      return this.organizer.website_pay;
    }
    return 'Swish'; // Default payment method
  }

  getDescription(): string {
    if (this.organizer && this.organizer.description) {
      return this.organizer.description;
    }
    return '';
  }

  getTrackLink(): string {
    return this.formData.link || this.track?.LINK_TO_TRACK || '';
  }

  isRegistrationClosed(): boolean {
    // For preview purposes, show as open
    return false;
  }

  getRegistrationButtonText(): string {
    if (this.isRegistrationClosed()) {
      return 'ANMÄLAN STÄNGD';
    }
    return this.isStripeEnabled() ? 'ANMÄLAN & BETALNING' : 'HÄMTA LOGIN';
  }

  isStripeEnabled(): boolean {
    return !!(this.formData.stripe_payment);
  }

  private getSafeLogoUrl(logoSvg: string | undefined): string {
    if (!logoSvg) return '';

    // If it's already a data URL, return as is
    if (logoSvg.startsWith('data:image/svg+xml')) {
      return logoSvg;
    }

    // If it's base64 encoded SVG, create data URL
    if (logoSvg && !logoSvg.includes('<svg')) {
      return `data:image/svg+xml;base64,${logoSvg}`;
    }

    // If it's raw SVG, encode it
    if (logoSvg && logoSvg.includes('<svg')) {
      const base64 = btoa(logoSvg);
      return `data:image/svg+xml;base64,${base64}`;
    }

    return '';
  }
}
