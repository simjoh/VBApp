import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {FileUpload} from "primeng/fileupload";
import {UploadService} from "../../../core/upload.service";
import {environment} from "../../../../environments/environment";
import { MessageService } from 'primeng/api';

@Component({
  selector: 'brevet-upload-track',
  templateUrl: './upload-track.component.html',
  styleUrls: ['./upload-track.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UploadTrackComponent implements OnInit {

  @ViewChild('primeFileUpload') primeFileUpload: FileUpload;

  uploadedFiles: any[] = [];

  constructor( private uploadService: UploadService) { }

  ngOnInit(): void {
  }


  myUploader($event: any) {
    console.log("onUpload() START");
    for(let file of $event.files) {
      let progress = this.uploadService.upload( environment.backend_url + "buildlEventAndTrackFromCsv/upload" , new Set($event.files));
      console.log("FILE TO BE UPLOADED: ", file);
      this.primeFileUpload.onProgress.emit({ originalEvent: null, progress: 100 });
      this.uploadedFiles.push(file);
    }
  }

  progressReport($event: any) {
    this.primeFileUpload.progress = $event;
  }

  removeFile(file: File, uploader: FileUpload) {
    const index = uploader.files.indexOf(file);
    uploader.remove(null, index);
  }

}
