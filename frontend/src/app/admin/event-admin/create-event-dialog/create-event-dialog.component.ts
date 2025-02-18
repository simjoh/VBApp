import { DatePipe } from '@angular/common';
import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { NgForm } from '@angular/forms';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {EventRepresentation} from "../../../shared/api/api";

@Component({
    selector: 'brevet-create-event-dialog',
    templateUrl: './create-event-dialog.component.html',
    styleUrls: ['./create-event-dialog.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class CreateEventDialogComponent implements OnInit {

  eventForm: EventFormModel;
  es: any;
  eventStatus: any;
  categories: any[] = [{name: 'Aktiv', key: 'A'}, {name: 'Inställd', key: 'I'}, {name: 'Utförd', key: 'U'}];


  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig,public datepipe: DatePipe) { }

  ngOnInit(): void {
   this.eventForm = this.createObject();

    this.eventStatus = this.categories[0];
  }

  private createObject(): EventFormModel{
    return {
      event_uid: "",
      title: "",
      startdate: null,
      endddate: null
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
        active: false,
        canceled: false,
        completed: false,
        description: eventForm.controls.description.value
      } as EventRepresentation
  }

  changeStatus(event: any) {

    if (this.eventStatus.key === "A") {
      this.eventForm.active = true;
      this.eventForm.completed = false;
      this.eventForm.canceled = false;
      this.eventStatus = this.categories[0];
    }

    if (this.eventStatus.key === "U") {
      this.eventForm.active = false;
      this.eventForm.completed = true;
      this.eventForm.canceled = false;
      this.eventStatus = this.categories[2];
    }

    if (this.eventStatus.key === "I") {
      this.eventForm.active = false;
      this.eventForm.completed = false;
      this.eventForm.canceled = true;
      this.eventStatus = this.categories[1];
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
