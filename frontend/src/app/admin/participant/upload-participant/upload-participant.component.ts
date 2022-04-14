import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {FileUpload} from "primeng/fileupload";
import {UploadService} from "../../../core/upload.service";
import {ParticipantComponentService} from "../participant-component.service";
import {map, tap} from "rxjs/operators";
import {environment} from "../../../../environments/environment";

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

  constructor( private uploadService: UploadService, private participantcomponentservice: ParticipantComponentService) { }

  ngOnInit(): void {
  }

  myUploader($event: any) {
    console.log($event.files);
    for(let file of $event.files) {
        let progress = this.uploadService.upload(environment.backend_url + "participants/upload/track/" + this.trackuid , new Set($event.files));
        console.log("FILE TO BE UPLOADED: ", file);
        this.primeFileUpload.onProgress.emit(100 / 100 * 100);
        this.uploadedFiles.push(file);

    }
  }

  updateTtrack($event: any) {
    console.log($event);
      this.trackuid = $event
  }
}
