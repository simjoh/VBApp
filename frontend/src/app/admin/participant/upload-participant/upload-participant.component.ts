import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {FileUpload} from "primeng/fileupload";
import {UploadService} from "../../../core/upload.service";
import {ParticipantComponentService} from "../participant-component.service";
import {map, tap} from "rxjs/operators";
import {environment} from "../../../../environments/environment";
import {MessageService} from "primeng/api";

@Component({
  selector: 'brevet-upload-participant',
  templateUrl: './upload-participant.component.html',
  styleUrls: ['./upload-participant.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UploadParticipantComponent implements OnInit {

  @ViewChild('primeFileUpload') primeFileUpload: FileUpload;

  trackuid: string;

  valtbana: boolean = false;

  trackuid$ = this.participantcomponentservice.track$.toPromise().then((s) => {
      if (s.length === 36){
        this.valtbana = true;
      } else {
        this.valtbana = false;
      }
  });


  uploadedFiles: any[] = [];

  constructor( private uploadService: UploadService, private participantcomponentservice: ParticipantComponentService,private messageService: MessageService) { }

  ngOnInit(): void {
  }

  myUploader($event: any) {
    console.log($event.files);
    for(let file of $event.files) {
        let progress = this.uploadService.upload(environment.backend_url + "participants/upload/track/" + this.trackuid , new Set($event.files));

      progress[$event.files[0].name].progress.pipe(map((ss) => {
        this.uploadedFiles.push(file);
this.primeFileUpload.onProgress.emit({ originalEvent: null, progress: ss });
      })).subscribe();
        console.log("FILE TO BE UPLOADED: ", file);
    }
  }

  updateTtrack($event: any) {
    console.log($event);
      this.trackuid = $event
  }

  progressReport($event: any) {
    this.primeFileUpload.progress = $event;
  }

  removeFile(file: File, uploader: FileUpload) {
    const index = uploader.files.indexOf(file);
    uploader.remove(null, index);
  }
}
