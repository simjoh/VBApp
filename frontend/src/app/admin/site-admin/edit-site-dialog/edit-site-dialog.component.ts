import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {UploadService} from "../../../core/upload.service";
import {NgForm} from "@angular/forms";
import {FileUpload} from "primeng/fileupload";
import {SiteFormModel} from "../create-site-dialog/create-site-dialog.component";
import {SiteRepresentation, User} from "../../../shared/api/api";
import {environment} from "../../../../environments/environment";

@Component({
  selector: 'brevet-edit-site-dialog',
  templateUrl: './edit-site-dialog.component.html',
  styleUrls: ['./edit-site-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EditSiteDialogComponent implements OnInit {

  @ViewChild('primeFileUploadw') primeFileUpload: FileUpload;

  uploadedFiles: any[] = [];

  siteform: SiteRepresentation;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig, private uploadService: UploadService) { }

  ngOnInit(): void {
    this.siteform = this.config.data.user;

    // Handle coordinate data that might be objects
    if (this.siteform) {
      this.normalizeCoordinates();
    }
  }

  private normalizeCoordinates(): void {
    // At this point we know siteform exists due to the check in ngOnInit
    const site = this.siteform as SiteRepresentation;

    // Convert coordinate objects to strings if necessary
    if (site.lat != null && typeof site.lat === 'object') {
      site.lat = site.lat!.toString();
    }
    if (site.lng != null && typeof site.lng === 'object') {
      site.lng = site.lng!.toString();
    }

    // Ensure coordinates are strings, not null or undefined
    site.lat = site.lat?.toString() ?? "";
    site.lng = site.lng?.toString() ?? "";

    // Handle the location field if it exists as an object
    if (site.location && typeof site.location === 'object') {
      const location = site.location as any;
      if (location.lat && location.lat !== null && !site.lat) {
        site.lat = location.lat.toString();
      }
      if (location.lng && location.lng !== null && !site.lng) {
        site.lng = location.lng.toString();
      }
    }
  }

  addEvent(siteForm: NgForm) {
    if (siteForm.valid){
      this.ref.close(this.siteform);
    } else {
      siteForm.dirty
    }
  }

  cancel() {
    this.ref.close(null);
  }

  myUploader(event) {
    this.siteform.image = environment.pictureurl + "/" + event.files[0].name;
    console.log("onUpload() START");
    for(let file of event.files) {
      let progress = this.uploadService.upload(environment.backend_url + "site/upload" , new Set(event.files));
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
