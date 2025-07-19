import { Component, OnInit, ChangeDetectionStrategy, LOCALE_ID } from '@angular/core';
import { registerLocaleData } from '@angular/common';
import localeSv from '@angular/common/locales/sv';
import {TrackBuilderComponentService} from "../track-builder-component.service";
import { OrganizerService } from '../../../organizer-admin/organizer.service';

// Register Swedish locale
registerLocaleData(localeSv);

@Component({
  selector: 'brevet-track-builder-track-info-form',
  templateUrl: './track-builder-track-info-form.component.html',
  styleUrls: ['./track-builder-track-info-form.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [
    { provide: LOCALE_ID, useValue: 'sv-SE' }
  ]
})
export class TrackBuilderTrackInfoFormComponent implements OnInit {

  model = new EventTrackInfo(0,"", "", "", "", "", 0);
  selectedOrganizer: any = null;
  stripeEnabled: boolean = false;
  isSuperUser = false;

  // Event type options
  eventTypeOptions = [
    { label: 'Brevet Randonneur Mondiaux', value: 'BRM' },
    { label: 'Brevet Populaire', value: 'BP' },
    { label: 'Midnight Sun Randonnée', value: 'MSR' }
  ];

  selectedEventType: string = 'BRM'; // Default to BRM

  // Distance dropdown options with official brevet distances
  distanceOptions = [
    { label: '100 km', value: 100 },
    { label: '200 km', value: 200 },
    { label: '300 km', value: 300 },
    { label: '400 km', value: 400 },
    { label: '600 km', value: 600 },
    { label: '1000 km', value: 1000 },
    { label: '1200 km', value: 1200 },
    { label: '1400 km', value: 1400 }
  ];

  selectedDistance: number | null = null;

  // Swedish locale for PrimeNG calendar
  sv: any = {
    firstDayOfWeek: 1,
    dayNames: ["söndag", "måndag", "tisdag", "onsdag", "torsdag", "fredag", "lördag"],
    dayNamesShort: ["sön", "mån", "tis", "ons", "tor", "fre", "lör"],
    dayNamesMin: ["sö", "må", "ti", "on", "to", "fr", "lö"],
    monthNames: ["januari", "februari", "mars", "april", "maj", "juni", "juli", "augusti", "september", "oktober", "november", "december"],
    monthNamesShort: ["jan", "feb", "mar", "apr", "maj", "jun", "jul", "aug", "sep", "okt", "nov", "dec"],
    today: 'Idag',
    clear: 'Rensa'
  };

  constructor(
    private trackbuildercomponentService: TrackBuilderComponentService,
    private organizerService: OrganizerService
  ) { }

  ngOnInit(): void {
    this.checkUserRoles();
    this.add();

    // Initialize selected distance if model has a value
    if (this.model.trackdistance > 0) {
      this.selectedDistance = this.model.trackdistance;
    }

    // Set default start time if empty - p-calendar expects Date object for timeOnly
    if (!this.model.starttime) {
      const defaultTime = new Date();
      defaultTime.setHours(7, 0, 0, 0); // 07:00
      this.model.starttime = defaultTime;
    } else if (typeof this.model.starttime === 'string') {
      // Convert string time to Date object for p-calendar
      this.model.starttime = this.stringTimeToDate(this.model.starttime);
    }

    // Set default start date if empty (tomorrow) - p-calendar expects Date object
    if (!this.model.startdate) {
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      this.model.startdate = tomorrow;
    } else if (typeof this.model.startdate === 'string') {
      // Convert string date to Date object for p-calendar
      this.model.startdate = this.stringDateToDate(this.model.startdate);
    }
  }

  private checkUserRoles(): void {
    const currentUser = JSON.parse(localStorage.getItem('activeUser') || '{}');
    this.isSuperUser = currentUser.roles?.includes('SUPERUSER');

    // If user is not superuser, pre-select their organizer_id and load organizer data
    if (!this.isSuperUser && currentUser.organizer_id) {
      this.model.organizer_id = currentUser.organizer_id;

      // Load the organizer data for the pre-selected organizer
      this.loadOrganizerData(currentUser.organizer_id);
    }
  }

  private loadOrganizerData(organizerId: number): void {
    this.organizerService.getOrganizers().subscribe(organizers => {
      const organizer = organizers.find(org => org.id === organizerId);
      if (organizer) {
        this.selectedOrganizer = organizer;
        // Store in the track builder service for summary access
        this.trackbuildercomponentService.setOrganizer(organizer);
      }
    });
  }

  private formatDateToYYYYMMDD(date: Date): string {
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  private stringTimeToDate(timeString: string): Date {
    const time = new Date();
    const [hours, minutes] = timeString.split(':').map(num => parseInt(num) || 0);
    time.setHours(hours, minutes, 0, 0);
    return time;
  }

  private dateToTimeString(date: Date): string {
    if (!date) return "07:00";
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
  }

  private stringDateToDate(dateString: string): Date {
    if (!dateString) return new Date();
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? new Date() : date;
  }

  private dateToDateString(date: Date): string {
    if (!date) {
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      return this.formatDateToYYYYMMDD(tomorrow);
    }
    return this.formatDateToYYYYMMDD(date);
  }

  private dateToDateTimeString(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}`;
  }

  addEvent($event: any) {
        this.model.event_uid = $event;
        this.trackbuildercomponentService.choosenEvent(this.model.event_uid);
  }

  onDistanceChange(event: any) {
    // Handle both dropdown selection and manual input
    const value = event.value || event.target?.value;
    if (value) {
      this.model.trackdistance = parseInt(value.toString());
      this.selectedDistance = this.model.trackdistance;
      this.add(); // Trigger update
    }
  }

  onTimeChange(event: any) {
    // Ensure time is in 24-hour format
    const timeValue = event.target.value;
    if (timeValue) {
      this.model.starttime = this.formatTo24Hour(timeValue);
      this.add();
    }
  }

  onOrganizerChange(organizerId: number) {
    // Handle organizer ID selection
    this.model.organizer_id = organizerId;
    this.add();
  }

  onOrganizerObjectChange(organizer: any) {
    // Handle organizer object selection for preview
    this.selectedOrganizer = organizer;
    // Also store in the track builder service for summary access
    this.trackbuildercomponentService.setOrganizer(organizer);
    // Update form data immediately
    this.updateFormData();
  }

  // Real-time update methods for individual fields
  onTrackNameChange() {
    this.updateFormData();
  }

  onStartDateChange() {
    this.updateFormData();
  }

  onStartTimeChange() {
    this.updateFormData();
  }

  onDescriptionChange() {
    this.updateFormData();
  }

  onStartLocationChange() {
    this.updateFormData();
  }

  onPaymentChange() {
    this.updateFormData();
  }

  onLinkChange() {
    this.updateFormData();
  }

  onStripeChange() {
    this.updateFormData();
  }

  onRegistrationOpensChange() {
    this.updateFormData();
  }

  onRegistrationClosesChange() {
    this.updateFormData();
  }

  onElevationChange() {
    this.updateFormData();
  }

  onEventTypeChange() {
    this.model.event_type = this.selectedEventType;
    this.updateFormData();
  }

  add() {
    // Convert Date objects to strings if needed
    let formattedTime: string;
    if (this.model.starttime instanceof Date) {
      formattedTime = this.dateToTimeString(this.model.starttime);
    } else {
      formattedTime = this.formatTo24Hour(this.model.starttime || "07:00");
    }

    let formattedDate: string;
    if (this.model.startdate instanceof Date) {
      formattedDate = this.dateToDateString(this.model.startdate);
    } else {
      formattedDate = this.model.startdate || this.dateToDateString(new Date());
    }

    this.trackbuildercomponentService.rusaInput(
      {
        event_distance: this.model.trackdistance,
        start_time: formattedTime,
        start_date: formattedDate,
        event_uid: "",
        track_title: this.model.trackname,
        controls: [],
        link: this.model.link
      }
    );

    // Also send form data for real-time preview updates
    this.updateFormData();
  }

  private updateFormData() {
    // Convert Date objects to strings for preview
    let formattedTime: string;
    if (this.model.starttime instanceof Date) {
      formattedTime = this.dateToTimeString(this.model.starttime);
    } else {
      formattedTime = this.formatTo24Hour(this.model.starttime || "07:00");
    }

    let formattedDate: string;
    if (this.model.startdate instanceof Date) {
      formattedDate = this.dateToDateString(this.model.startdate);
    } else {
      formattedDate = this.model.startdate || this.dateToDateString(new Date());
    }

    // Format registration dates
    let formattedRegistrationOpens: string = '';
    if (this.model.registration_opens instanceof Date) {
      formattedRegistrationOpens = this.dateToDateTimeString(this.model.registration_opens);
    } else if (this.model.registration_opens) {
      formattedRegistrationOpens = this.model.registration_opens;
    }

    let formattedRegistrationCloses: string = '';
    if (this.model.registration_closes instanceof Date) {
      formattedRegistrationCloses = this.dateToDateTimeString(this.model.registration_closes);
    } else if (this.model.registration_closes) {
      formattedRegistrationCloses = this.model.registration_closes;
    }

    const formData = {
      trackname: this.model.trackname,
      trackdistance: this.model.trackdistance,
      starttime: formattedTime,
      startdate: formattedDate,
      startdateRaw: this.model.startdate,
      link: this.model.link,
      organizer_id: this.model.organizer_id,
      description: this.model.description,
      startLocation: this.model.startLocation,
      payment: this.model.payment,
      stripe_payment: this.stripeEnabled,
      registration_opens: formattedRegistrationOpens,
      registration_closes: formattedRegistrationCloses,
      elevation: this.model.elevation,
      event_type: this.selectedEventType
    };

    this.trackbuildercomponentService.updateAllFormData(formData);
  }

    private formatTo24Hour(timeString: string): string {
    if (!timeString) return "07:00";

    // Remove any mask characters and clean the string
    const cleanTime = timeString.replace(/[^\d:]/g, '');

    // If already in HH:MM format, validate and return
    if (/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(cleanTime)) {
      const [hours, minutes] = cleanTime.split(':');
      const h = parseInt(hours);
      const m = parseInt(minutes);

      // Validate ranges
      if (h >= 0 && h <= 23 && m >= 0 && m <= 59) {
        return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
      }
    }

    // Try to parse incomplete time (e.g., "7:30" -> "07:30")
    const timeMatch = cleanTime.match(/^(\d{1,2}):?(\d{0,2})$/);
    if (timeMatch) {
      const hours = parseInt(timeMatch[1] || '0');
      const minutes = parseInt(timeMatch[2] || '0');

      if (hours >= 0 && hours <= 23 && minutes >= 0 && minutes <= 59) {
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
      }
    }

    return "07:00"; // Default fallback
  }

  // Event preview methods
  shouldShowPreview(): boolean {
    return !!(this.model.trackname && this.model.trackdistance);
  }

  getEventTitle(): string {
    return this.model.trackname || 'Arrangemangsnnam';
  }

  getDistanceDisplay(): string {
    return this.model.trackdistance ? `${this.model.trackdistance} KM` : 'N/A';
  }

  getElevationDisplay(): string {
    // Calculate estimated elevation based on distance (rough estimate)
    const estimatedElevation = this.model.trackdistance ? Math.round(this.model.trackdistance * 8.5) : 0;
    return estimatedElevation > 0 ? `${estimatedElevation} M` : 'N/A';
  }

  getStartDateDisplay(): string {
    if (!this.model.startdate) return '';

    if (this.model.startdate instanceof Date) {
      const date = this.model.startdate;
      const day = date.getDate();
      const monthNames = ["Januari", "Februari", "Mars", "April", "Maj", "Juni",
                         "Juli", "Augusti", "September", "Oktober", "November", "December"];
      const month = monthNames[date.getMonth()];
      return `${day} ${month}`;
    }

    // Handle string date
    const date = new Date(this.model.startdate);
    if (!isNaN(date.getTime())) {
      const day = date.getDate();
      const monthNames = ["Januari", "Februari", "Mars", "April", "Maj", "Juni",
                         "Juli", "Augusti", "September", "Oktober", "November", "December"];
      const month = monthNames[date.getMonth()];
      return `${day} ${month}`;
    }

    return this.model.startdate;
  }

  getStartTimeDisplay(): string {
    if (!this.model.starttime) return '07:00';

    if (this.model.starttime instanceof Date) {
      return this.dateToTimeString(this.model.starttime);
    }

    return this.formatTo24Hour(this.model.starttime);
  }

  getLastRegistrationDisplay(): string {
    if (!this.model.startdate) return '';

    let startDate: Date;
    if (this.model.startdate instanceof Date) {
      startDate = this.model.startdate;
    } else {
      startDate = new Date(this.model.startdate);
    }

    if (!isNaN(startDate.getTime())) {
      const lastReg = new Date(startDate);
      lastReg.setDate(startDate.getDate() - 1); // Day before start
      const day = lastReg.getDate();
      const monthNames = ["Januari", "Februari", "Mars", "April", "Maj", "Juni",
                         "Juli", "Augusti", "September", "Oktober", "November", "December"];
      const month = monthNames[lastReg.getMonth()];
      return `${day} ${month}`;
    }

    return '';
  }

  getStartLocationDisplay(): string {
    return this.model.startLocation || 'Startplatsen, Stad';
  }

  getOrganizerDisplay(): string {
    if (this.selectedOrganizer) {
      return this.selectedOrganizer.name || this.selectedOrganizer.organizername || 'Vald arrangör';
    }
    return this.model.organizer_id ? 'Vald arrangör' : 'Ingen arrangör vald';
  }

  getPaymentDisplay(): string {
    return this.model.payment || 'Swish 123 456 78 90';
  }

  getDescription(): string {
    return this.model.description || '';
  }

  isRegistrationClosed(): boolean {
    // For preview purposes, show as open
    return false;
  }

  getRegistrationButtonText(): string {
    return this.isRegistrationClosed() ? 'ANMÄLAN STÄNGD' : 'HÄMTA LOGIN';
  }

  getOrganizerLogo(): string {
    if (this.selectedOrganizer && this.selectedOrganizer.logo_svg) {
      return this.getSafeLogoUrl(this.selectedOrganizer.logo_svg);
    }
    return '';
  }

  getOrganizerWebsite(): string {
    if (this.selectedOrganizer && this.selectedOrganizer.website) {
      return this.selectedOrganizer.website;
    }
    return '';
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

    // If it's raw SVG, encode it properly handling Unicode characters
    if (logoSvg && logoSvg.includes('<svg')) {
      try {
        // Use encodeURIComponent to handle Unicode characters properly
        const encodedSvg = encodeURIComponent(logoSvg);
        return `data:image/svg+xml;charset=utf-8,${encodedSvg}`;
      } catch (error) {
        console.error('Error encoding SVG:', error);
        return '';
      }
    }

    return '';
  }
}

export class EventTrackInfo {
  constructor(
    public trackdistance: number,
    public trackname: string,
    public event_uid: string,
    public starttime?: any, // Can be string or Date
    public startdate?: any, // Can be string or Date
    public link?: string,
    public organizer_id?: number,
    public description?: string,
    public startLocation?: string,
    public payment?: string,
    public registration_opens?: any, // Can be string or Date
    public registration_closes?: any, // Can be string or Date
    public elevation?: number, // Height difference in meters
    public event_type: string = 'BRM', // Default to BRM
  ) {  }

}
