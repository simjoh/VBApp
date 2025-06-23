import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {map, take} from "rxjs/operators";
import {EventRepresentation, Site, SiteRepresentation, User} from "../../../shared/api/api";
import {Observable} from "rxjs";
import {EventService} from "../event.service";
import {CreateUserDialogComponent} from "../../user-admin/create-user-dialog/create-user-dialog.component";
import {ConfirmationService, PrimeNGConfig} from "primeng/api";
import {DialogService} from "primeng/dynamicdialog";
import {DeviceDetectorService} from "ngx-device-detector";
import {CreateEventDialogComponent} from "../create-event-dialog/create-event-dialog.component";
import {LinkService} from "../../../core/link.service";
import {EditSiteDialogComponent} from "../../site-admin/edit-site-dialog/edit-site-dialog.component";
import {EditEventDialogComponent} from "../edit-event-dialog/edit-event-dialog.component";

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
              private deviceDetector: DeviceDetectorService,
              private linkService: LinkService) { }

  ngOnInit(): void {
  }

  openNew() {

    let width;
    if ( this.deviceDetector.isDesktop()){
      width = "400px";
    } else {
      width = "95%"
    }

    const ref = this.dialogService.open(CreateEventDialogComponent, {
      data: {
        id: '51gF3'
      },
      header: 'Lägg till Evenemang',
      width: width,
      height: 'auto',
      contentStyle: { 'overflow': 'visible' }
    });

    ref.onClose.subscribe((event: EventRepresentation) => {
      if (event) {
        this.eventService.newEvent(event);
      }
    });

  }

  editProduct(user_uid: any) {

    let width;
    if ( this.deviceDetector.isDesktop()){
      width = "400px";
    } else {
      width = "95%"
    }

    const editref = this.dialogService.open(EditEventDialogComponent, {
      data: {
        event: user_uid,
        id: '51gF3'
      },
      header: 'Redigera Evenemang',
      width: width,
      height: 'auto',
      contentStyle: { 'overflow': 'visible' }
    });

    editref.onClose.pipe(take(1)).subscribe(((event: EventRepresentation) => {
      if (event) {
        console.log(event);
        this.eventService.updateEvent(event.event_uid, event);
      } else {
        editref.destroy();
      }

    }));
  }

  deleteProduct(event_uid: any) {
    this.confirmationService.confirm({
      message: 'Är du säker på att du vill ta bort ' + event_uid + '?',
      header: 'Bekräfta',
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

  canDelete(site: any):boolean {
    return this.linkService.exists(site.links,"relation.event.delete");
  }
}
