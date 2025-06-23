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

  city: any;



  categories: any[] = [{name: 'Aktiv', key: 'A'}, {name: 'Inställd', key: 'I'}, {name: 'Utförd', key: 'U'}];

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {
  }

  ngOnInit(): void {
    this.city = this.categories[1];
    this.eventForm = this.config.data.event;

    if (this.eventForm.active === true) {
      this.city = this.categories[0];
    }

    if (this.eventForm.completed === true) {
      this.city = this.categories[2];
    }

    if (this.eventForm.canceled === true) {
      this.city = this.categories[1];
    }
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

    if (this.city.key === "A") {
      this.eventForm.active = true;
      this.eventForm.completed = false;
      this.eventForm.canceled = false;
      this.city = this.categories[0];
    }

    if (this.city.key === "U") {
      this.eventForm.active = false;
      this.eventForm.completed = true;
      this.eventForm.canceled = false;
      this.city = this.categories[2];
    }

    if (this.city.key === "I") {
      this.eventForm.active = false;
      this.eventForm.completed = false;
      this.eventForm.canceled = true;
      this.city = this.categories[1];
    }
  }
}
