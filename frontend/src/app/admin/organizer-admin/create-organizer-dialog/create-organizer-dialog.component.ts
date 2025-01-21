import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {ButtonDirective} from "primeng/button";
import {CalendarModule} from "primeng/calendar";
import {FormsModule, NgForm} from "@angular/forms";
import {InputTextModule} from "primeng/inputtext";
import {InputTextareaModule} from "primeng/inputtextarea";
import {DatePipe, NgForOf} from "@angular/common";
import {PaginatorModule} from "primeng/paginator";
import {RadioButtonModule} from "primeng/radiobutton";
import {Ripple} from "primeng/ripple";
import {TooltipModule} from "primeng/tooltip";
import {EventFormModel} from "../../event-admin/create-event-dialog/create-event-dialog.component";
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {EventRepresentation, OrganizerRepresentation} from "../../../shared/api/api";

@Component({
  selector: 'brevet-create-organizer-dialog',
  standalone: true,
    imports: [
        ButtonDirective,
        CalendarModule,
        FormsModule,
        InputTextModule,
        InputTextareaModule,
        NgForOf,
        PaginatorModule,
        RadioButtonModule,
        Ripple,
        TooltipModule
    ],
  templateUrl: './create-organizer-dialog.component.html',
  styleUrl: './create-organizer-dialog.component.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateOrganizerDialogComponent implements OnInit{

  eventForm: OrganizerFormModel;


  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {
  }

  ngOnInit(): void {
    this.eventForm = this.createObject();
  }


  private createObject(): OrganizerFormModel{
    return {
      organizer_id: null,
      name: "",
      email: null,
      contact_person: "",
      phone: null
    } as unknown as OrganizerFormModel;

  }
  addEvent(organizerForm: NgForm) {
    if (organizerForm.valid){
      this.ref.close(this.getUserObject(organizerForm));
    } else {
      organizerForm.dirty
    }
  }

  private getUserObject(eventForm: NgForm) {
    return {
      organizer_id: null,
      name: eventForm.controls.name.value,
      contact_person: eventForm.controls.contact_person.value,
      email: eventForm.controls.email.value,
      phone: eventForm.controls.phone.value,
    } as unknown as OrganizerRepresentation;
  }

  cancel() {
    this.ref.close(null);
  }
}

export class OrganizerFormModel {
  organizer_id: number;
  name: string;
  contact_person: string;
  email: boolean;
  phone: boolean;
}

