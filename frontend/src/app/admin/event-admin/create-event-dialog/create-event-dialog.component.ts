import { DatePipe } from '@angular/common';
import { Component, OnInit, ChangeDetectionStrategy, inject } from '@angular/core';
import { NgForm } from '@angular/forms';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {EventRepresentation} from "../../../shared/api/api";
import { TranslationService } from '../../../core/services/translation.service';

@Component({
  selector: 'brevet-create-event-dialog',
  templateUrl: './create-event-dialog.component.html',
  styleUrls: ['./create-event-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateEventDialogComponent implements OnInit {
  private translationService = inject(TranslationService);

  eventForm: EventFormModel;
  es: any;
  eventStatus: any;
  categories: any[] = [];

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig,public datepipe: DatePipe) { }

  ngOnInit(): void {
   this.eventForm = this.createObject();

    // Initialize categories with translations
    this.categories = [
      {name: this.translationService.translate('event.statusActive'), key: 'A'},
      {name: this.translationService.translate('event.statusCancelled'), key: 'I'},
      {name: this.translationService.translate('event.statusCompleted'), key: 'U'}
    ];

    // Set default status to "Aktiv" (Active)
    this.eventStatus = this.categories[0];

    // Initialize the form with the default status
    this.updateFormStatus(this.eventStatus);
  }

  private createObject(): EventFormModel{
    return {
      event_uid: "",
      title: "",
      startdate: null,
      endddate: null,
      active: true,  // Default to active
      canceled: false,
      completed: false,
      description: ""
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
        description: eventForm.controls.description.value
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
  completed: boolean
}
