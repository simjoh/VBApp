import {ChangeDetectionStrategy, Component} from '@angular/core';
import {AsyncPipe, NgIf} from "@angular/common";
import {ButtonDirective} from "primeng/button";
import {ConfirmDialogModule} from "primeng/confirmdialog";
import {InputTextModule} from "primeng/inputtext";
import {PrimeTemplate} from "primeng/api";
import {Ripple} from "primeng/ripple";
import {SharedModule} from "../../../shared/shared.module";
import {TableModule} from "primeng/table";
import {map} from "rxjs/operators";
import {EventRepresentation, OrganizerRepresentation} from "../../../shared/api/api";
import {Observable} from "rxjs";
import {OrganizerService} from "../organizer.service";
import {OrganizerInfoPopoverComponent} from "../organizer-info-popover/organizer-info-popover.component";
import {CreateEventDialogComponent} from "../../event-admin/create-event-dialog/create-event-dialog.component";
import {DialogService} from "primeng/dynamicdialog";
import {DeviceDetectorService} from "ngx-device-detector";
import {CreateOrganizerDialogComponent} from "../create-organizer-dialog/create-organizer-dialog.component";

@Component({
  selector: 'brevet-organizer-list',
  standalone: true,
  imports: [
    AsyncPipe,
    ButtonDirective,
    ConfirmDialogModule,
    InputTextModule,
    NgIf,
    PrimeTemplate,
    Ripple,
    SharedModule,
    TableModule,
    OrganizerInfoPopoverComponent
  ],
  templateUrl: './organizer-list.component.html',
  styleUrl: './organizer-list.component.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class OrganizerListComponent {


  organizers$ = this.organizerservice.allOrganizers$.pipe(
    map((s: Array<OrganizerRepresentation>) => {
      // this.table. = 0;
      return s;
    })
  ) as Observable<OrganizerRepresentation[]>;

  constructor(private organizerservice: OrganizerService, private dialogService: DialogService, private deviceDetector: DeviceDetectorService,) {
  }


  viewUser(user_uid: any) {

  }

  openNew() {
    let width;
    if (this.deviceDetector.isDesktop()) {
      width = "60%";
    } else {
      width = "90%"
    }

    const ref = this.dialogService.open(CreateOrganizerDialogComponent, {
      data: {
        id: '51gF3'
      },
      header: 'Lägg till en arrangör',
    });

    ref.onClose.subscribe((event: OrganizerRepresentation) => {
      if (event) {
        this.organizerservice.newOrganizer(event);
      }
    });
  }
}
