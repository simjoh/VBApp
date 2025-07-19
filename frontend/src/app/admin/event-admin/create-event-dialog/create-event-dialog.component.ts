import { DatePipe } from '@angular/common';
import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { NgForm } from '@angular/forms';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {EventRepresentation} from "../../../shared/api/api";

@Component({
  selector: 'brevet-create-event-dialog',
  templateUrl: './create-event-dialog.component.html',
  styleUrls: ['./create-event-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateEventDialogComponent implements OnInit {

  eventForm: EventFormModel;
  es: any;
  eventStatus: any;
  categories: any[] = [{name: 'Aktiv', key: 'A'}, {name: 'Inställd', key: 'I'}, {name: 'Utförd', key: 'U'}];
  isSuperUser = false;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig,public datepipe: DatePipe) { }

  ngOnInit(): void {
   this.eventForm = this.createObject();
   this.checkUserRoles();

    // Set default status to "Aktiv" (Active)
    this.eventStatus = this.categories[0];

    // Initialize the form with the default status
    this.updateFormStatus(this.eventStatus);
  }

  private checkUserRoles(): void {
    const activeUser = JSON.parse(localStorage.getItem('activeUser') || '{}');
    this.isSuperUser = activeUser.roles?.includes('SUPERUSER') || false;
  }

  private createObject(): EventFormModel{
    // Get current user from localStorage
    const currentUser = JSON.parse(localStorage.getItem('activeUser') || '{}');
    const isSuperUser = currentUser.roles?.includes('SUPERUSER');

    // Preselect organizer_id if user is not superuser and has an organizer_id
    let preselectedOrganizerId: number | undefined = undefined;
    if (!isSuperUser && currentUser.organizer_id) {
      preselectedOrganizerId = currentUser.organizer_id;
    }

    return {
      event_uid: "",
      title: "",
      startdate: null,
      endddate: null,
      active: true,  // Default to active
      canceled: false,
      completed: false,
      description: "",
      organizer_id: preselectedOrganizerId
    } as unknown as EventFormModel;
  }

  addEvent(eventForm: NgForm) {

    if (eventForm.valid){
      this.ref.close(this.getUserObject(eventForm));
    } else {
      eventForm.dirty
    }
  }

  cancel() {
    this.ref.close(null);
  }

  private getUserObject(eventForm: NgForm) {
      return {
        event_uid: "",
        title: eventForm.controls.title.value,
        startdate: this.datepipe.transform(eventForm.controls.startdate.value, 'yyyy-MM-dd'),
        enddate: this.datepipe.transform(eventForm.controls.endddate.value, 'yyyy-MM-dd'),
        active: this.eventForm.active,
        canceled: this.eventForm.canceled,
        completed: this.eventForm.completed,
        description: eventForm.controls.description.value,
        organizer_id: this.eventForm.organizer_id
      } as EventRepresentation
  }

  changeStatus(event: any) {
    // Use the event.value which contains the selected category
    const selectedStatus = event.value;
    this.updateFormStatus(selectedStatus);
  }

  private updateFormStatus(selectedStatus: any) {
    if (selectedStatus.key === "A") {
      this.eventForm.active = true;
      this.eventForm.completed = false;
      this.eventForm.canceled = false;
    } else if (selectedStatus.key === "U") {
      this.eventForm.active = false;
      this.eventForm.completed = true;
      this.eventForm.canceled = false;
    } else if (selectedStatus.key === "I") {
      this.eventForm.active = false;
      this.eventForm.completed = false;
      this.eventForm.canceled = true;
    }
  }
}


export class EventFormModel {
  event_uid: string;
  title: string;
  active: boolean;
  canceled: boolean;
  startdate: Date;
  endddate: Date;
  description: string;
  completed: boolean;
  organizer_id?: number;
}
