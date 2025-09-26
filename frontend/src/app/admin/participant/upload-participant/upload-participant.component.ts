import {Component, OnInit, ChangeDetectionStrategy, ViewChild, ChangeDetectorRef, inject} from '@angular/core';
import {FileUpload} from "primeng/fileupload";
import {UploadService} from "../../../core/upload.service";
import {environment} from "../../../../environments/environment";
import {map} from "rxjs/operators";
import {ParticipantComponentService} from "../participant-component.service";
import {MessageService} from "primeng/api";
import {TrackService} from "../../../shared/track-service";
import {TranslationService} from "../../../core/services/translation.service";

interface UploadStats {
  total_rows: number;
  successful: number;
  failed: number;
  skipped: number;
  errors: Array<{
    row: number;
    message: string;
    data: any[];
  }>;
  participants: any[];
}

@Component({
  selector: 'brevet-upload-participant',
  templateUrl: './upload-participant.component.html',
  styleUrls: ['./upload-participant.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UploadParticipantComponent implements OnInit {
  private translationService = inject(TranslationService);

  @ViewChild('primeFileUpload') primeFileUpload: FileUpload;

  trackuid: string = '';
  valtbana: boolean = false;
  uploadedFiles: any[] = [];
  uploadStats: UploadStats | null = null;
  showStats: boolean = false;
  selectedTrack: any = null;

  trackuid$ = this.participantcomponentservice.track$.toPromise().then((s) => {
      if (s.length === 36){
        this.valtbana = true;
      } else {
        this.valtbana = false;
      }
  });

  constructor(
    private uploadService: UploadService,
    private participantcomponentservice: ParticipantComponentService,
    private messageService: MessageService,
    private cdr: ChangeDetectorRef,
    private trackService: TrackService
  ) { }

  ngOnInit(): void {
  }

  myUploader($event: any) {
    for(let file of $event.files) {
        let progress = this.uploadService.upload(environment.backend_url + "participants/upload/track/" + this.trackuid , new Set($event.files));

      progress[$event.files[0].name].progress.pipe(
        map((ss) => {
          this.uploadedFiles.push(file);
          this.primeFileUpload.onProgress.emit({ originalEvent: null, progress: ss });
        })
      ).subscribe();

      // Subscribe to the response to get upload statistics
      progress[$event.files[0].name].response.subscribe((response: any) => {
        // Handle the upload response with statistics
        if (response && typeof response === 'object') {
          // Check if it's an error response
          if (response.error) {
            this.messageService.add({
              severity: 'error',
              summary: this.translationService.translate('upload.uploadFailed'),
              detail: response.error
            });
            return;
          }

          this.uploadStats = response as UploadStats;
          this.showStats = true;

          // Trigger change detection to update the view
          this.cdr.detectChanges();

          // Show summary message
          if (this.uploadStats.successful > 0) {
            this.messageService.add({
              severity: 'success',
              summary: this.translationService.translate('upload.uploadComplete'),
              detail: `${this.translationService.translate('upload.successful')} ${this.uploadStats.successful} ${this.translationService.translate('upload.participant')}`
            });
          }

          if (this.uploadStats.failed > 0 || this.uploadStats.skipped > 0) {
            this.messageService.add({
              severity: 'warn',
              summary: this.translationService.translate('upload.uploadIssues'),
              detail: `${this.uploadStats.failed} ${this.translationService.translate('upload.failed')}, ${this.uploadStats.skipped} ${this.translationService.translate('upload.skipped')}`
            });
          }
        }
      });
    }
  }

  updateTtrack($event: any) {
      this.trackuid = $event;

      // Fetch track information to check if it's active
      if (this.trackuid) {
        this.trackService.getTrack(this.trackuid).subscribe(
          (trackInfo: any) => {
            this.selectedTrack = trackInfo;
            this.cdr.detectChanges();
          },
          (error) => {
            console.error('Error fetching track info:', error);
            this.selectedTrack = null;
          }
        );
      } else {
        this.selectedTrack = null;
      }
  }

  progressReport($event: any) {
    this.primeFileUpload.progress = $event;
  }

  removeFile(file: File, uploader: FileUpload) {
    const index = uploader.files.indexOf(file);
    uploader.remove(null, index);
  }

  hideStats() {
    this.showStats = false;
    this.uploadStats = null;
    this.cdr.detectChanges();
  }

  getSeverityClass(): string {
    if (!this.uploadStats) return '';

    if (this.uploadStats.failed > 0) return 'error';
    if (this.uploadStats.skipped > 0) return 'warn';
    return 'success';
  }
}
