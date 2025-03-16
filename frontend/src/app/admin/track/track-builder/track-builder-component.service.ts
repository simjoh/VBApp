import {Injectable} from '@angular/core';
import {map, mergeMap, switchMap, take, tap} from "rxjs/operators";
import {RusaTimeCalculationApiService} from "./rusa-time-calculation-api.service";
import {BehaviorSubject, combineLatest, of} from "rxjs";
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
      // Skip if any required data is missing
      if (!rusaplanner || !event || !event.event_uid) {
        return null;
      }

      // Sort controls by distance before sending to API
      // This ensures accurate time calculations regardless of the order they were added
      const sortedControls = this.sortControlsByDistance([...controls]);

      // Create a simple input object with sorted controls
      return {
        ...rusaplanner,
        event_uid: event.event_uid,
        use_acp_calculator: true,
        controls: sortedControls
      };
    }),
    switchMap((input) => {
      if (!input) {
        return of(null);
      }

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
    // Update the controls subject - no need to sort here
    // The sorting will happen in the $all observable before sending to API
    this.$rusaPlannerControlsSubject.next(controls);
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
      // Get the current data
      const event = await this.$currentEvent.pipe(take(1)).toPromise();
      const rusaplanner = await this.$rusaPlannerInput.pipe(take(1)).toPromise();
      const controls = await this.$rusaPlannerControlsInput.pipe(take(1)).toPromise();

      // Check if we have all the required data
      if (!event || !event.event_uid || !rusaplanner || !controls || controls.length === 0) {
        this.messageService.add({
          key: 'tc',
          severity: 'error',
          summary: 'Fel',
          detail: 'Saknar nödvändig information för att skapa banan.'
        });
        return false;
      }

      // Sort controls by distance before sending to API
      const sortedControls = this.sortControlsByDistance([...controls]);

      // Create the input data with sorted controls
      const inputData = {
        ...rusaplanner,
        event_uid: event.event_uid,
        use_acp_calculator: true,
        controls: sortedControls
      };

      // Call the API directly
      const trackData = await this.rusatimeService.addSite(inputData).toPromise();

      if (!trackData) {
        this.messageService.add({
          key: 'tc',
          severity: 'error',
          summary: 'Fel',
          detail: 'Kunde inte skapa banan.'
        });
        return false;
      }

      // Save the track
      await this.trackService.createTrack(trackData as RusaPlannerResponseRepresentation);

      this.messageService.add({
        key: 'tc',
        severity: 'success',
        summary: 'Bana sparad',
        detail: 'Banan har sparats framgångsrikt!'
      });

      return true;
    } catch (error) {
      console.error('Error creating track:', error);
      this.messageService.add({
        key: 'tc',
        severity: 'error',
        summary: 'Sparande misslyckades',
        detail: 'Det gick inte att spara banan. Försök igen senare.'
      });
      return false;
    }
  }
}
