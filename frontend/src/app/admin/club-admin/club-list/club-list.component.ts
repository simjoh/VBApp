import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef, inject } from '@angular/core';
import { map, take } from "rxjs/operators";
import { ClubRepresentation } from "../../../shared/api/api";
import { Observable, BehaviorSubject, combineLatest } from "rxjs";
import { ClubService } from "../club.service";
import { ConfirmationService, PrimeNGConfig } from "primeng/api";
import { DialogService } from "primeng/dynamicdialog";
import { DeviceDetectorService } from "ngx-device-detector";
import { LinkService } from "../../../core/link.service";
import { CreateClubDialogComponent } from "../create-club-dialog/create-club-dialog.component";
import { EditClubDialogComponent } from "../edit-club-dialog/edit-club-dialog.component";
import { TranslationService } from '../../../core/services/translation.service';

@Component({
  selector: 'brevet-club-list',
  templateUrl: './club-list.component.html',
  styleUrls: ['./club-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ClubListComponent implements OnInit {
  private translationService = inject(TranslationService);

  showOnlyWithAcpCode = false;
  private filterTrigger = new BehaviorSubject<void>(undefined);

  clubs$ = this.clubService.clubsWithAdd$.pipe(
    map((s: Array<ClubRepresentation>) => {
      return s;
    })
  ) as Observable<ClubRepresentation[]>;

  filteredClubs$ = combineLatest([this.clubs$, this.filterTrigger]).pipe(
    map(([clubs]) => {
      if (this.showOnlyWithAcpCode) {
        return clubs.filter(club =>
          club.acp_kod &&
          club.acp_kod !== '0' &&
          club.acp_kod.trim() !== ''
        );
      }
      return clubs.map(club => ({
        ...club,
        acp_kod: club.acp_kod?.trim() || null
      }));
    })
  );

  constructor(private clubService: ClubService,
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
    return this.showOnlyWithAcpCode ? 'Visa Alla' : 'Visa med ACP-kod';
  }

  get filterButtonIcon(): string {
    return this.showOnlyWithAcpCode ? 'pi pi-list' : 'pi pi-filter';
  }

  get filterButtonStyleClass(): string {
    return this.showOnlyWithAcpCode ? 'p-button-outlined p-button-secondary' : 'p-button-outlined p-button-info';
  }

  toggleAcpCodeFilter() {
    this.showOnlyWithAcpCode = !this.showOnlyWithAcpCode;
    this.filterTrigger.next(undefined);
    this.cdr.detectChanges();
  }

  openNew() {
    let width;
    if (this.deviceDetector.isDesktop()) {
      width = "400px";
    } else {
      width = "95%"
    }

    const ref = this.dialogService.open(CreateClubDialogComponent, {
      data: {
        id: '51gF3'
      },
      header: this.translationService.translate('dialog.addClub'),
      width: width,
      height: 'auto',
      contentStyle: { 'overflow': 'visible' }
    });

    ref.onClose.subscribe((club: ClubRepresentation) => {
      if (club) {
        this.clubService.newClub(club);
      }
    });
  }

  editClub(club: ClubRepresentation) {
    let width;
    if (this.deviceDetector.isDesktop()) {
      width = "400px";
    } else {
      width = "95%"
    }

    const editref = this.dialogService.open(EditClubDialogComponent, {
      data: {
        club: club,
        id: '51gF3'
      },
      header: 'Redigera Klubb',
      width: width,
      height: 'auto',
      contentStyle: { 'overflow': 'visible' }
    });

    editref.onClose.pipe(take(1)).subscribe(((club: ClubRepresentation) => {
      if (club) {
        this.clubService.updateClub(club.club_uid, club);
      } else {
        editref.destroy();
      }
    }));
  }

  deleteClub(club_uid: string) {
    this.confirmationService.confirm({
      message: 'Är du säker på att du vill ta bort denna klubb?',
      header: 'Bekräfta',
      icon: 'pi pi-exclamation-triangle',
      accept: () => {
        this.clubService.deleteClub(club_uid);
      },
      reject: () => {
        // User rejected deletion
      }
    });
  }

  canDelete(club: any): boolean {
    return this.linkService.exists(club.links, "relation.club.delete");
  }
}
