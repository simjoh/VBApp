import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {map} from "rxjs/operators";
import {EventRepresentation, Site, User} from "../../../shared/api/api";
import {Observable} from "rxjs";
import {EventService} from "../event.service";
import {CreateUserDialogComponent} from "../../user-admin/create-user-dialog/create-user-dialog.component";
import {ConfirmationService, PrimeNGConfig} from "primeng/api";
import {DialogService} from "primeng/dynamicdialog";
import {DeviceDetectorService} from "ngx-device-detector";
import {CreateEventDialogComponent} from "../create-event-dialog/create-event-dialog.component";

@Component({
  selector: 'brevet-event-list',
  templateUrl: './event-list.component.html',
  styleUrls: ['./event-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush

})
export class EventListComponent implements OnInit {


  events$ = this.eventService.eventsWithAdd$.pipe(
    map((s:Array<EventRepresentation>) => {
      // this.table. = 0;
      return s;
    })
  ) as Observable<EventRepresentation[]>;

  constructor(private eventService: EventService,
              private primengConfig: PrimeNGConfig,
              private dialogService: DialogService,
              private confirmationService: ConfirmationService,
              private deviceDetector: DeviceDetectorService) { }

  ngOnInit(): void {
  }

  openNew() {

    let width;
    if ( this.deviceDetector.isDesktop()){
      width = "60%";
    } else {
      width = "80%"
    }

    const ref = this.dialogService.open(CreateEventDialogComponent, {
      data: {
        id: '51gF3'
      },
      header: 'Lägg till event',
    });

    ref.onClose.subscribe((event: EventRepresentation) => {
      if (event) {
        this.eventService.newEvent(event);
      }
    });

  }

  editProduct(user_uid: any) {

  }

  deleteProduct(event_uid: any) {
    this.confirmationService.confirm({
      message: 'Are you sure you want to delete ' + event_uid + '?',
      header: 'Confirm',
      icon: 'pi pi-exclamation-triangle',
      accept: () => {
        console.log(event_uid)
        this.eventService.deleterEvent(event_uid);
      },
      reject: () => {
        console.log("reject");
      }
    });
  }
}
