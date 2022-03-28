import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {FileUpload} from "primeng/fileupload";
import {UploadService} from "../../../core/upload.service";
import {ParticipantComponentService} from "../participant-component.service";
import {map} from "rxjs/operators";

@Component({
  selector: 'brevet-upload-participant',
  templateUrl: './upload-participant.component.html',
  styleUrls: ['./upload-participant.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UploadParticipantComponent implements OnInit {

  @ViewChild('primeFileUpload') primeFileUpload: FileUpload;


  trackuid$ = this.participantcomponentservice.track$.pipe(
    map((track) => {
          return true;
    })
  )

  uploadedFiles: any[] = [];

  constructor( private uploadService: UploadService, private participantcomponentservice: ParticipantComponentService) { }

  ngOnInit(): void {
  }

  myUploader($event: any) {
    console.log("onUpload() START");
    for(let file of $event.files) {
      let progress = this.uploadService.upload("/api/participants/upload/track/uid" , new Set($event.files));
      console.log("FILE TO BE UPLOADED: ", file);
      this.primeFileUpload.onProgress.emit(100 / 100 * 100);
      this.uploadedFiles.push(file);
    }
  }
}
