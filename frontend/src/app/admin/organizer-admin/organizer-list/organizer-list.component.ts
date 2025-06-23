import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { map, take } from "rxjs/operators";
import { OrganizerRepresentation } from "../organizer.service";
import { Observable, BehaviorSubject, combineLatest } from "rxjs";
import { OrganizerService } from "../organizer.service";
import { ConfirmationService, PrimeNGConfig } from "primeng/api";
import { DialogService } from "primeng/dynamicdialog";
import { DeviceDetectorService } from "ngx-device-detector";
import { LinkService } from "../../../core/link.service";
import { CreateOrganizerDialogComponent } from "../create-organizer-dialog/create-organizer-dialog.component";
import { EditOrganizerDialogComponent } from "../edit-organizer-dialog/edit-organizer-dialog.component";

@Component({
  selector: 'brevet-organizer-list',
  templateUrl: './organizer-list.component.html',
  styleUrls: ['./organizer-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class OrganizerListComponent implements OnInit {

  showOnlyActive = false;
  private filterTrigger = new BehaviorSubject<void>(undefined);

  organizers$ = this.organizerService.organizers$.pipe(
    map((s: Array<OrganizerRepresentation>) => {
      return s;
    })
  ) as Observable<OrganizerRepresentation[]>;

  filteredOrganizers$ = combineLatest([this.organizers$, this.filterTrigger]).pipe(
    map(([organizers]) => {
      if (this.showOnlyActive) {
        const filtered = organizers.filter(organizer => organizer.active);
        return filtered;
      }
      return organizers;
    })
  );

  constructor(private organizerService: OrganizerService,
              private primengConfig: PrimeNGConfig,
              private dialogService: DialogService,
              private confirmationService: ConfirmationService,
              private deviceDetector: DeviceDetectorService,
              private linkService: LinkService,
              private cdr: ChangeDetectorRef) { }

  ngOnInit(): void {
    // Initialize the filter trigger
    this.filterTrigger.next(undefined);
  }

  get filterButtonLabel(): string {
    return this.showOnlyActive ? 'Visa Alla' : 'Visa Endast Aktiva';
  }

  get filterButtonIcon(): string {
    return this.showOnlyActive ? 'pi pi-list' : 'pi pi-filter';
  }

  get filterButtonStyleClass(): string {
    return this.showOnlyActive ? 'p-button-outlined p-button-secondary' : 'p-button-outlined p-button-info';
  }

  toggleActiveFilter() {
    this.showOnlyActive = !this.showOnlyActive;
    this.filterTrigger.next(undefined);
    this.cdr.detectChanges();
  }

  openNew() {
    let width;
    if (this.deviceDetector.isDesktop()) {
      width = "800px";
    } else {
      width = "90%"
    }

    const ref = this.dialogService.open(CreateOrganizerDialogComponent, {
      data: {
        id: '51gF3'
      },
      header: 'Skapa Arrangör',
      width: width,
      height: 'auto',
      maximizable: false,
      resizable: false,
      contentStyle: { 'overflow': 'visible' }
    });

    ref.onClose.subscribe((organizer: OrganizerRepresentation) => {
      if (organizer) {
        this.organizerService.createOrganizer(organizer);
      }
    });
  }

  editOrganizer(organizer: OrganizerRepresentation) {
    let width;
    if (this.deviceDetector.isDesktop()) {
      width = "800px";
    } else {
      width = "90%"
    }

    const editref = this.dialogService.open(EditOrganizerDialogComponent, {
      data: {
        organizer: organizer,
        id: '51gF3'
      },
      header: 'Redigera Arrangör',
      width: width,
      height: 'auto',
      maximizable: false,
      resizable: false,
      contentStyle: { 'overflow': 'visible' }
    });

    editref.onClose.pipe(take(1)).subscribe(((organizer: OrganizerRepresentation) => {
      if (organizer && organizer.id) {
        this.organizerService.updateOrganizer(organizer.id, organizer).subscribe({
          next: (updatedOrganizer) => {
            console.log('Organizer updated successfully:', updatedOrganizer);
          },
          error: (error) => {
            console.error('Error updating organizer:', error);
            // You might want to show a toast message here
          }
        });
      } else {
        if (organizer && !organizer.id) {
          console.error('Cannot update organizer: ID is missing');
        }
        editref.destroy();
      }
    }));
  }

  deleteOrganizer(organizer_id: number) {
    this.confirmationService.confirm({
      message: 'Är du säker på att du vill ta bort denna arrangör?',
      header: 'Bekräfta',
      icon: 'pi pi-exclamation-triangle',
      accept: () => {
        this.organizerService.deleteOrganizer(organizer_id);
      },
      reject: () => {
        console.log("reject");
      }
    });
  }

  canDelete(organizer: any): boolean {
    return this.linkService.exists(organizer.links, "relation.organizer.delete");
  }
}
