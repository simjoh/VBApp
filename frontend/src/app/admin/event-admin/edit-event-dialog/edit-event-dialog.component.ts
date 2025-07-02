import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {NgForm} from "@angular/forms";
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {EventRepresentation} from "../../../shared/api/api";

@Component({
  selector: 'brevet-edit-event-dialog',
  templateUrl: './edit-event-dialog.component.html',
  styleUrls: ['./edit-event-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EditEventDialogComponent implements OnInit {

  eventForm: EventRepresentation;

  eventStatus: any;

  categories: any[] = [{name: 'Aktiv', key: 'A'}, {name: 'Inställd', key: 'I'}, {name: 'Utförd', key: 'U'}];

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {
  }

  ngOnInit(): void {
    this.eventForm = this.config.data.event;
    console.log('EditEventDialog ngOnInit - initial event data:', {
      event_uid: this.eventForm.event_uid,
      title: this.eventForm.title,
      active: this.eventForm.active,
      canceled: this.eventForm.canceled,
      completed: this.eventForm.completed
    });

    // Set the correct initial status based on the event data
    if (this.eventForm.active === true) {
      this.eventStatus = this.categories[0]; // Aktiv
      console.log('Setting initial status to Aktiv');
    } else if (this.eventForm.completed === true) {
      this.eventStatus = this.categories[2]; // Utförd
      console.log('Setting initial status to Utförd');
    } else if (this.eventForm.canceled === true) {
      this.eventStatus = this.categories[1]; // Inställd
      console.log('Setting initial status to Inställd');
    } else {
      // Default to Aktiv if no status is set
      this.eventStatus = this.categories[0];
      console.log('Setting initial status to default (Aktiv)');
    }

    console.log('Initial eventStatus value:', this.eventStatus);
  }


  editEvent(siteForm: NgForm) {
    if (siteForm.valid) {
      // Format dates properly before sending to backend
      if (this.eventForm.startdate instanceof Date) {
        this.eventForm.startdate = this.formatDateForBackend(this.eventForm.startdate);
      }
      if (this.eventForm.enddate instanceof Date) {
        this.eventForm.enddate = this.formatDateForBackend(this.eventForm.enddate);
      }

      console.log('Submitting event form with data:', {
        event_uid: this.eventForm.event_uid,
        title: this.eventForm.title,
        startdate: this.eventForm.startdate,
        enddate: this.eventForm.enddate,
        active: this.eventForm.active,
        canceled: this.eventForm.canceled,
        completed: this.eventForm.completed,
        description: this.eventForm.description
      });

      this.ref.close(this.eventForm);
    } else {
      siteForm.dirty
    }
  }

  private formatDateForBackend(date: Date): string {
    if (!date) return '';

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
  }

  cancel() {
    this.ref.close(null);
  }

  changeStatus(event: any) {
    console.log('changeStatus called with event:', event);
    console.log('event.value:', event.value);

    // Use the event.value which contains the selected category
    const selectedCategory = event.value;
    console.log('selectedCategory:', selectedCategory);

    // Reset all status fields first
    this.eventForm.active = false;
    this.eventForm.completed = false;
    this.eventForm.canceled = false;

    // Set the correct status based on selection
    if (selectedCategory && selectedCategory.key === "A") {
      this.eventForm.active = true;
    } else if (selectedCategory && selectedCategory.key === "U") {
      this.eventForm.completed = true;
    } else if (selectedCategory && selectedCategory.key === "I") {
      this.eventForm.canceled = true;
    }

    console.log('Updated eventForm status:', {
      active: this.eventForm.active,
      completed: this.eventForm.completed,
      canceled: this.eventForm.canceled
    });
  }

  // Alternative method to handle radio button changes
  onRadioChange() {
    console.log('onRadioChange called - eventStatus value:', this.eventStatus);
    if (this.eventStatus) {
      this.changeStatus({ value: this.eventStatus });
    }
  }
}
