import {Injectable} from '@angular/core';
import {map, mergeMap, switchMap, take, tap} from "rxjs/operators";
import {RusaTimeCalculationApiService} from "./rusa-time-calculation-api.service";
import {BehaviorSubject, combineLatest, firstValueFrom, of} from "rxjs";
import {MessageService} from "primeng/api";

import {EventService} from "../../shared/service/event.service";
import {EventRepresentation} from "../../../shared/api/api";
import { HttpClient } from "@angular/common/http";
import {
  RusaPlannerControlInputRepresentation,
  RusaPlannerInputRepresentation, RusaPlannerResponseRepresentation,
  RusaTimeRepresentation
} from "../../../shared/api/rusaTimeApi";
import { TrackService } from 'src/app/shared/track-service';

@Injectable()
export class TrackBuilderComponentService {

  $choosenEventSubject = new BehaviorSubject<string>("0");
  $choosenEventUid = this.$choosenEventSubject.asObservable();

  $organizerSubject = new BehaviorSubject<any>(null);
  $organizer = this.$organizerSubject.asObservable();

  // Track form data for real-time preview updates
  $formDataSubject = new BehaviorSubject<any>({});
  $formData = this.$formDataSubject.asObservable();

  $currentEvent = this.$choosenEventUid.pipe(
    switchMap((term) => {
      if (term != "0") {
        return this.eventService.getEvent(term)
      } else {
        return of({} as EventRepresentation);
      }
    }),
    map((res1: EventRepresentation) => {
      if (res1) {
        return res1;
      } else {
        return {} as EventRepresentation;
      }
    }),
    map((res: EventRepresentation) => {
      return res;
    })
  )

  $rusaPlannerInputSubject = new BehaviorSubject<RusaPlannerInputRepresentation>({} as RusaPlannerInputRepresentation)
  $rusaPlannerInput = this.$rusaPlannerInputSubject.asObservable()

  $test = this.$rusaPlannerInput.pipe(
    map((input1) => {
      if (input1.event_distance){
        return input1;
      } else {
        return null;
      }
    }),
    mergeMap((input) => {
      if (input){
        return this.rusatimeService.addSite(input);
      } else {
        return of(null);
      }
    }),
    map((response) => {
      return response;
    })
  );


  $rusaPlannerControlsSubject = new BehaviorSubject<RusaPlannerControlInputRepresentation[]>([])
  $rusaPlannerControlsInput = this.$rusaPlannerControlsSubject.asObservable()


  $summarySubject = new BehaviorSubject<RusaTimeRepresentation>(null);


  $all = combineLatest([this.$currentEvent, this.$rusaPlannerInput, this.$rusaPlannerControlsInput]).pipe(
    map(([event, rusaplanner, controls]) => {
      console.log('$all combineLatest data:', { event, rusaplanner, controls });

      // Skip if any required data is missing
      if (!rusaplanner || !event || !event.event_uid) {
        console.log('Missing required data, skipping API call');
        return null;
      }

      // Sort controls by distance before sending to API
      // This ensures accurate time calculations regardless of the order they were added
      const sortedControls = this.sortControlsByDistance([...controls]);

      // Create a simple input object with sorted controls
      const inputData = {
        ...rusaplanner,
        event_uid: event.event_uid,
        use_acp_calculator: true,
        controls: sortedControls
      };

      console.log('API input data prepared:', inputData);
      return inputData;
    }),
    switchMap((input) => {
      if (!input) {
        return of(null);
      }

      console.log('Calling rusatimeService.addSite with:', input);
      // Call the API
      return this.rusatimeService.addSite(input);
    })
  );


  $current = new BehaviorSubject<boolean>(false);
  aktuell = this.$current.asObservable().pipe(
    map((s) => {
      return this.rusatimeService.addSite(null);
    }),
    mergeMap((dd) => {
      return dd
    })
  )

  constructor(private rusatimeService: RusaTimeCalculationApiService, private trackService: TrackService, private eventService: EventService,  private httpClient: HttpClient, private messageService: MessageService) {
  }


  read() {
    this.$current.next(true)
  }

  choosenEvent(eventUid: string) {
    this.$choosenEventSubject.next(eventUid);
  }

  rusaInput(rusainput: RusaPlannerInputRepresentation){
    this.$rusaPlannerInputSubject.next(rusainput);
  }

  addControls(controls: Array<RusaPlannerControlInputRepresentation>) {
    console.log('Service addControls called with:', controls);
    // Update the controls subject - no need to sort here
    // The sorting will happen in the $all observable before sending to API
    this.$rusaPlannerControlsSubject.next(controls);
  }

  setOrganizer(organizer: any) {
    this.$organizerSubject.next(organizer);
  }

  updateFormData(fieldName: string, value: any) {
    const currentData = this.$formDataSubject.getValue();
    const updatedData = { ...currentData, [fieldName]: value };
    this.$formDataSubject.next(updatedData);
  }

  updateAllFormData(formData: any) {
    this.$formDataSubject.next(formData);
  }

  /**
   * Sorts controls by distance in ascending order
   * This ensures accurate time calculations regardless of the order controls were added
   */
  private sortControlsByDistance(controls: Array<RusaPlannerControlInputRepresentation>): Array<RusaPlannerControlInputRepresentation> {
    if (!controls || controls.length <= 1) {
      return controls;
    }

    // Create a copy to avoid modifying the original
    return [...controls].sort((a, b) => {
      const distanceA = a.DISTANCE === null ? 0 : a.DISTANCE;
      const distanceB = b.DISTANCE === null ? 0 : b.DISTANCE;
      return distanceA - distanceB;
    });
  }

  /**
   * Updates a single control and triggers recalculation
   * @param control The control to update
   * @param index The index of the control in the array
   */
  updateSingleControl(control: RusaPlannerControlInputRepresentation, index: number): void {
    // Get the current controls
    const currentControls = this.$rusaPlannerControlsSubject.getValue();

    // Check if the index is valid
    if (index >= 0 && index < currentControls.length) {
      // Create a new array with the updated control
      const updatedControls = [
        ...currentControls.slice(0, index),
        control,
        ...currentControls.slice(index + 1)
      ];

      // Update the controls subject
      this.$rusaPlannerControlsSubject.next(updatedControls);
    }
  }

  async createTrack(): Promise<boolean> {
    try {
      // Get latest form data
      const formData = this.$formDataSubject.getValue();
      const eventUid = this.$choosenEventSubject.getValue();
      const rusaInput = this.$rusaPlannerInputSubject.getValue();
      const controls = this.$rusaPlannerControlsSubject.getValue();

      // Validate required data
      if (!formData || !eventUid || eventUid === "0" || !rusaInput || !controls) {
        console.error('Missing required data for track creation');
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Missing required data for track creation'
        });
        return false;
      }

      // Sort controls by distance before sending to API
      const sortedControls = this.sortControlsByDistance([...controls]);

      // Create the track data with sorted controls
      const trackData = await firstValueFrom(this.rusatimeService.addSite({
        ...rusaInput,
        event_uid: eventUid,
        use_acp_calculator: true,
        controls: sortedControls
      }));

      if (!trackData) {
        throw new Error('Failed to generate track data');
      }

      // Create request payload with both track data and form data
      const requestPayload = {
        trackData: trackData,
        formData: {
          ...formData,
          event_type: formData.event_type || 'BRM' // Add event type with default
        }
      };

      // Save the track with form data for loppservice integration
      // Since createTrackWithFormData returns a Promise, we use await directly
      const response = await this.trackService.createTrackWithFormData(requestPayload);

      if (response) {
        this.messageService.add({
          severity: 'success',
          summary: 'Success',
          detail: 'Track created successfully'
        });
        return true;
      } else {
        throw new Error('Failed to create track');
      }
    } catch (error) {
      console.error('Error creating track:', error);
      this.messageService.add({
        severity: 'error',
        summary: 'Error',
        detail: error.message || 'Failed to create track'
      });
      return false;
    }
  }

  getFormData(): any {
    return this.$formDataSubject.getValue();
  }
}
