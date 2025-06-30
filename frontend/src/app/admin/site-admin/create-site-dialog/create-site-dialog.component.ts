import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {NgForm} from "@angular/forms";
import { FileUpload } from 'primeng/fileupload';
import {UploadService} from "../../../core/upload.service";
import {environment} from "../../../../environments/environment";

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
  selectedUnit: string = 'm';
  private originalValue: number = 900;

  constructor(
    private ref: DynamicDialogRef,
    private config: DynamicDialogConfig,
    private uploadService: UploadService
  ) {
    this.siteform = this.createObject();
  }

  ngOnInit(): void {
    this.siteform = this.createObject();
  }

  private createObject(): SiteFormModel{
    return {
      site_uid: "",
      event_uid: "",
      place: "",
      adress: "",
      lat: "",
      lng: "",
      description: "",
      picture: "",
      check_in_distance: "900"
    } as unknown as SiteFormModel;
  }

  onUnitChange(event: any) {
    const value = parseFloat(this.siteform.check_in_distance);
    if (isNaN(value)) return;

    if (event.value === 'km' && this.selectedUnit === 'm') {
      // Convert from m to km
      this.siteform.check_in_distance = (value / 1000).toFixed(3);
    } else if (event.value === 'm' && this.selectedUnit === 'km') {
      // Convert from km to m
      this.siteform.check_in_distance = Math.round(value * 1000).toString();
    }
  }

  addEvent(form: NgForm) {
    if (form.valid) {
      // Convert meters to kilometers before saving
      const valueInMeters = parseFloat(this.siteform.check_in_distance);
      if (!isNaN(valueInMeters)) {
        const valueInKm = valueInMeters / 1000;
        this.siteform.check_in_distance = valueInKm.toFixed(3);
      }
      this.ref.close(this.siteform);
    }
  }

  cancel() {
    this.ref.close(null);
  }

  onupload(event: any) {
    console.log(event);
  }

  myUploader(event) {
    this.siteform.image =  environment.pictureurl + "/" + event.files[0].name;
    console.log("onUpload() START");
    for(let file of event.files) {
      let progress = this.uploadService.upload("/api/site/upload" , new Set(event.files));
      console.log("FILE TO BE UPLOADED: ", file);
this.primeFileUpload.onProgress.emit({ originalEvent: null, progress: 100 });
      this.uploadedFiles.push(file);
    }
   // this.messageService.add({severity: 'info', summary: 'File Uploaded', detail: ''});
  }

  progressReport($event: any) {
    this.primeFileUpload.progress = $event;
  }
}

export class SiteFormModel extends NgForm{
  site_uid: string;
  event_uid: string;
  image: string;
  lat: string;
  lng: string;
  description: string;
  place: string;
  adress: string;
  check_in_distance: string;
}
