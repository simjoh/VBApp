import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {NgForm} from "@angular/forms";
import {EventFormModel} from "../../event-admin/create-event-dialog/create-event-dialog.component";
import { FileUpload } from 'primeng/fileupload';
import {UploadService} from "../../../core/upload.service";

@Component({
  selector: 'brevet-create-site-dialog',
  templateUrl: './create-site-dialog.component.html',
  styleUrls: ['./create-site-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateSiteDialogComponent implements OnInit {

  @ViewChild('primeFileUpload') primeFileUpload: FileUpload;

  uploadedFiles: any[] = [];

  siteform: SiteFormModel;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig, private uploadService: UploadService) { }

  ngOnInit(): void {
    this.siteform = this.createObject();
  }

  private createObject(): SiteFormModel{
    return {
      event_uid: "",
      place: "",
      adress: "",
      lat: null,
      long: null,
      description: "",
      picture: ""
    } as unknown as SiteFormModel;

  }


  addEvent(siteForm: NgForm) {
    if (siteForm.valid){
      this.ref.close(this.getUserObject(siteForm));
    } else {
      siteForm.dirty
    }
  }


  private getUserObject(siteForm: NgForm) {
    return {
      site_uid: "",
      image: this.siteform.image,
      description: this.siteform.description,
      lat:  this.siteform.lat,
      lng:  this.siteform.long,
      place: this.siteform.place,
      adress: this.siteform.adress
    }
  }

  cancel() {
    this.ref.close(null);
  }

  onupload(event: any) {
    console.log(event);
  }

  myUploader(event) {
    this.siteform.image = event.files[0].name;
    console.log("onUpload() START");
    for(let file of event.files) {
      let progress = this.uploadService.upload("/api/site/upload" , new Set(event.files));
      console.log("FILE TO BE UPLOADED: ", file);
      this.primeFileUpload.onProgress.emit(100 / 100 * 100);
      this.uploadedFiles.push(file);
    }
   // this.messageService.add({severity: 'info', summary: 'File Uploaded', detail: ''});
  }

  progressReport($event: any) {
    this.primeFileUpload.progress = $event;
  }
}

export class SiteFormModel {
  event_uid: string;
  image: string;
  lat: string;
  long: string;
  description: string;
  place: string;
  adress: string;
}
