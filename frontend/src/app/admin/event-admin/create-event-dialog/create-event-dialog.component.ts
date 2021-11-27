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


  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig,public datepipe: DatePipe) { }

  ngOnInit(): void {
   this.eventForm = this.createObject();


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
