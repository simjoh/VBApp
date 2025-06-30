import {ChangeDetectionStrategy, Component, OnInit, ChangeDetectorRef} from '@angular/core';
import {ParticipantComponentService} from "../participant-component.service";
import {map, startWith} from "rxjs/operators";
import {ParticipantInformationRepresentation, ParticipantRepresentation, TrackRepresentation} from "../../../shared/api/api";
import {BehaviorSubject, interval} from "rxjs";
import {DialogService} from "primeng/dynamicdialog";
import {EditTimeDialogComponent} from "../edit-time-dialog/edit-time-dialog.component";
import {EditBrevenrDialogComponent} from "../edit-brevenr-dialog/edit-brevenr-dialog.component";
import {EditCompetitorInfoDialogComponent} from "../edit-competitor-info-dialog/edit-competitor-info-dialog.component";
import {environment} from "../../../../environments/environment";
import {HttpClient} from "@angular/common/http";
import { saveAs } from 'file-saver';
import { CompetitorInfoService, CompetitorInfo } from "../../../shared/competitor-info.service";
import { MessageService } from 'primeng/api';
import { TrackService } from '../../../shared/track-service';
import { LinkService } from '../../../core/link.service';
import { HttpMethod } from '../../../core/HttpMethod';
import { faTowerBroadcast } from '@fortawesome/free-solid-svg-icons';

@Component({
  selector: 'brevet-participant-table',
  templateUrl: './participant-table.component.html',
  styleUrls: ['./participant-table.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantTableComponent implements OnInit {

  currentTrackRepresentation: TrackRepresentation;

  $serachDisabledSubject = new BehaviorSubject(true)
  $searchDisabled = this.$serachDisabledSubject.asObservable()


  $participant = this.participantComponentService.$participantsfortrack.pipe(
    map((vals) => {
      this.searchDisabled(vals)
      return vals
    })
  );

  $dimmadnfbuttonSubject = new BehaviorSubject(true);
  $dimDnf = this.$dimmadnfbuttonSubject.asObservable();

  $dimmadnsbuttonSubject = new BehaviorSubject(true);
  $dimDns = this.$dimmadnsbuttonSubject.asObservable();
  intervalSub: any;

  faBroadcast = faTowerBroadcast;

  constructor(
    private participantComponentService: ParticipantComponentService,
    private dialogService: DialogService,
    private http: HttpClient,
    private competitorInfoService: CompetitorInfoService,
    private messageService: MessageService,
    private trackService: TrackService,
    private linkService: LinkService,
    private cdr: ChangeDetectorRef
  ) {
  }

  ngOnInit(): void {
    this.intervalSub = interval(60000).pipe(
      startWith(0),
    ).subscribe(data => this.participantComponentService.reload());
    this.$serachDisabledSubject.next(true);

    // Subscribe to track representation changes
    this.trackService.currentTrackRepresentation$.subscribe(track => {
      console.log('Track representation updated:', track);
      this.currentTrackRepresentation = track;
      this.cdr.detectChanges(); // Force change detection
    });
  }

  isPossibleToDelete(participant: ParticipantRepresentation) {
    return this.participantComponentService.isPossibleToRemove(participant);
  }

  remove(participant: any) {
    this.participantComponentService.remove(participant).then(() => {
      this.participantComponentService.reload();
    });

  }

  private searchDisabled(vals: ParticipantInformationRepresentation[]) {
    if (vals.length > 0) {
      this.$serachDisabledSubject.next(false);
    } else {
      this.$serachDisabledSubject.next(true);
    }
  }


  test(participant_uid: any) {
    this.participantComponentService.setCurrentparticipant(participant_uid)
  }

  currentparticipant(participant: ParticipantRepresentation) {
    this.test(participant);
  }

  dnf(participant: ParticipantRepresentation) {
    if (participant.dnf === true) {
      this.participantComponentService.rollbackdnf(participant);
    } else {
      this.participantComponentService.dnf(participant);
    }

  }

  textDnfButton(started: boolean, dnf: any): string {
    if (!started) {
      return "DNF";
    } else {
      if (!dnf) {
        return "DNF";
      } else {
        return "Ångra DNF";
      }
    }
  }

  textDnsButton(started: boolean, dns: boolean): string {
    if (!started) {
      if (dns === true) {
        return "Ångra DNS";
      } else {
        return "DNS";
      }
    } else {
      return "DNS";
    }
  }

  dns(participant: ParticipantRepresentation) {
    if (participant.dns === true) {
      this.participantComponentService.rollbackDns(participant);
    } else {
      this.participantComponentService.dns(participant);
    }

  }

  editTotalTime(participant: ParticipantRepresentation) {

    const ref = this.dialogService.open(EditTimeDialogComponent, {
      data: {
        time: participant.time
      },
      header: 'Ändra sluttid',
    });

    ref.onClose.subscribe((newTime: string) => {
      if (newTime) {
        participant.time = newTime
        this.participantComponentService.updateTime(participant);
        this.participantComponentService.reload();
      }
    });
  }


  editbrevenr(participant: ParticipantRepresentation) {
    console.log(participant.brevenr);
    const ref = this.dialogService.open(EditBrevenrDialogComponent, {
      data: {
        brevenr: participant.brevenr
      },
      header: 'Lägg till brevenr',
    });

    ref.onClose.subscribe((brevenr: string) => {
      if (brevenr) {
        participant.brevenr = brevenr
        this.participantComponentService.addbrevenr(participant);
        this.participantComponentService.reload();
      }
    });
  }

  getCurrentTrack():boolean {
    return false;
  }

  exportHomologation() {
    const trackUid = this.participantComponentService.getCurrentTrackUid();
    console.log('Track UID:', trackUid);

    if (!trackUid) {
      console.error('No track UID available');
      return;
    }

    const url = environment.backend_url + 'participants/track/' + trackUid + '/report/export';
    console.log('Export URL:', url);

    this.http.get(url, {
      responseType: 'blob',
      headers: {
        'Accept': 'text/csv; charset=utf-8'
      }
    }).subscribe({
      next: (response: Blob) => {
        console.log('Response received:', response);

        // Generate filename based on current date
        const date = new Date().toISOString().split('T')[0];
        const filename = `Homologation_${date}.csv`;

        saveAs(response, filename);
      },
      error: (error) => {
        console.error('Export error:', error);
      }
    });
  }

  exportStartList() {
    const trackUid = this.participantComponentService.getCurrentTrackUid();
    console.log('Track UID for start list:', trackUid);

    if (!trackUid) {
      console.error('No track UID available');
      return;
    }

    const url = environment.backend_url + 'participants/track/' + trackUid + '/startlist/export';
    console.log('Export Start List URL:', url);

    this.http.get(url, {
      responseType: 'blob',
      headers: {
        'Accept': 'text/csv; charset=utf-8'
      }
    }).subscribe({
      next: (response: Blob) => {
        console.log('Start list response received:', response);

        // Generate filename based on current date
        const date = new Date().toISOString().split('T')[0];
        const filename = `Participant_List_${date}.csv`;

        saveAs(response, filename);
      },
      error: (error) => {
        console.error('Start list export error:', error);
      }
    });
  }

  editCompetitorInfo(participant: any) {
    const competitorUid = participant.competitorRepresentation.competitor_uid;
    const currentInfo = participant.competitorInforepresentation;

    const ref = this.dialogService.open(EditCompetitorInfoDialogComponent, {
      data: {
        competitorInfo: currentInfo
      },
      header: 'Redigera kontaktinformation',
      width: '700px',
      modal: true,
      closable: true
    });

    ref.onClose.subscribe((updatedInfo: CompetitorInfo) => {
      if (updatedInfo) {
        this.competitorInfoService.updateCompetitorInfo(competitorUid, updatedInfo)
          .subscribe({
            next: (response) => {
              this.messageService.add({
                severity: 'success',
                summary: 'Framgång',
                detail: 'Kontaktinformation uppdaterad'
              });
              this.participantComponentService.reload();
            },
            error: (error) => {
              this.messageService.add({
                severity: 'error',
                summary: 'Fel',
                detail: 'Kunde inte uppdatera kontaktinformation'
              });
              console.error('Error updating competitor info:', error);
            }
          });
      }
    });
  }

  isCurrentTrackUnpublished(): boolean {
    console.log('=== Checking if track is unpublished ===');
    console.log('Current track representation:', this.currentTrackRepresentation);

    if (!this.currentTrackRepresentation) {
      console.log('No track representation available');
      return false;
    }

    console.log('Track links:', this.currentTrackRepresentation.links);
    console.log('Detailed links:', JSON.stringify(this.currentTrackRepresentation.links, null, 2));

    // Check for publish link (means track is unpublished)
    const isUnpublished = this.linkService.exists(this.currentTrackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT);
    console.log('Track unpublished status:', isUnpublished);

    // Debug link service
    const foundLink = this.linkService.findByRel(this.currentTrackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT);
    console.log('Found publish link:', foundLink);

    return isUnpublished;
  }

  /**
   * Check if the track has a publish link available
   * This is used to show/hide the publish button
   */
  hasPublishLink(): boolean {
    console.log('=== Checking for publish link ===');
    console.log('Current track representation:', this.currentTrackRepresentation);
    if (!this.currentTrackRepresentation || !this.currentTrackRepresentation.links) {
      console.log('No track representation or links available');
      return false;
    }
    const hasLink = this.linkService.exists(this.currentTrackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT);
    console.log('Has publish link:', hasLink);
    return hasLink;
  }

  /**
   * Check if the track has an unpublish link available
   * This is used to show/hide the unpublish button
   */
  hasUnpublishLink(): boolean {
    console.log('=== Checking for unpublish link ===');
    console.log('Current track representation:', this.currentTrackRepresentation);
    if (!this.currentTrackRepresentation || !this.currentTrackRepresentation.links) {
      console.log('No track representation or links available');
      return false;
    }
    const hasLink = this.linkService.exists(this.currentTrackRepresentation.links, 'relation.track.undopublisresults', HttpMethod.PUT);
    console.log('Has unpublish link:', hasLink);
    return hasLink;
  }

  isCurrentTrackPublished(): boolean {
    if (!this.currentTrackRepresentation || !this.currentTrackRepresentation.links) {
      return false;
    }
    return this.linkService.exists(this.currentTrackRepresentation.links, 'relation.track.undopublisresults', HttpMethod.PUT);
  }

  async publishCurrentTrack(): Promise<void> {
    if (!this.currentTrackRepresentation) {
      console.error('No track representation available');
      return;
    }

    try {
      console.log('Publishing track - before API call:', {
        trackUid: this.currentTrackRepresentation.track_uid,
        active: this.currentTrackRepresentation.active,
        links: this.currentTrackRepresentation.links?.map((l: any) => ({ rel: l.rel, method: l.method }))
      });

      // Disable buttons during the operation to prevent double-clicks
      const trackUid = this.currentTrackRepresentation.track_uid;
      const publishButton = document.querySelector(`[data-track-uid="${trackUid}"].publish-btn`) as HTMLButtonElement;
      const unpublishButton = document.querySelector(`[data-track-uid="${trackUid}"].unpublish-btn`) as HTMLButtonElement;

      if (publishButton) publishButton.disabled = true;
      if (unpublishButton) unpublishButton.disabled = true;

      try {
        // Check which action to perform based on current state
        // active=1 means unpublished, active=0 means published
        const isCurrentlyPublished = this.currentTrackRepresentation.active === 0;
        let updatedTrack: TrackRepresentation;

        if (isCurrentlyPublished) {
          // Track is published (active=0), so we should unpublish it
          // We send publish=true to set active=1 (unpublished)
          updatedTrack = await this.trackService.publishresult(this.currentTrackRepresentation);
        } else {
          // Track is unpublished (active=1), so we should publish it
          // We send publish=false to set active=0 (published)
          updatedTrack = await this.trackService.undopublishresult(this.currentTrackRepresentation);
        }

        console.log('Publishing track - API call successful:', updatedTrack);

        // Update the current track representation with the response
        this.currentTrackRepresentation = updatedTrack;

        // Show success message
        this.messageService.add({
          severity: 'success',
          summary: 'Success',
          detail: updatedTrack.active === 0 ? 'Track published successfully' : 'Track unpublished successfully'
        });

        // Also reload the participant list
        this.participantComponentService.reload();
        this.cdr.detectChanges(); // Force change detection

      } catch (error) {
        console.error('Error publishing/unpublishing track:', error);
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to update track publish status'
        });
      } finally {
        // Re-enable buttons after operation
        if (publishButton) publishButton.disabled = false;
        if (unpublishButton) unpublishButton.disabled = false;
      }
    } catch (error) {
      console.error('Error in publish operation:', error);
      this.messageService.add({
        severity: 'error',
        summary: 'Error',
        detail: 'An unexpected error occurred'
      });
    }
  }
}
