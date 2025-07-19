import {Component, OnInit, ViewChild} from '@angular/core';
import {DynamicDialogConfig, DynamicDialogRef} from "primeng/dynamicdialog";
import {NgForm} from "@angular/forms";
import {SiteRepresentation} from "../../../shared/api/api";
import {UploadService} from "../../../core/upload.service";
import { FileUpload } from 'primeng/fileupload';

@Component({
  selector: 'brevet-edit-site-dialog',
  templateUrl: './edit-site-dialog.component.html',
  styleUrls: ['./edit-site-dialog.component.scss']
})
export class EditSiteDialogComponent implements OnInit {
  @ViewChild('primeFileUpload') primeFileUpload: FileUpload;

  siteform: SiteRepresentation;
  uploadedFiles: any[] = [];
  isSuperUser = false;

  constructor(
    private ref: DynamicDialogRef,
    private config: DynamicDialogConfig,
    private uploadService: UploadService
  ) {
  }

  ngOnInit(): void {
    this.checkUserRoles();

    // Initialize form data
    this.siteform = this.config.data;

    // Convert check-in distance from km to meters
    if (this.siteform?.check_in_distance) {
      const valueInKm = parseFloat(this.siteform.check_in_distance);
      if (!isNaN(valueInKm)) {
        const valueInMeters = valueInKm * 1000;
        this.siteform.check_in_distance = valueInMeters.toString();
      }
    }

    // Handle coordinate data
    if (this.siteform) {
      this.normalizeCoordinates();
    }
  }

  private checkUserRoles(): void {
    const currentUser = JSON.parse(localStorage.getItem('activeUser') || '{}');
    this.isSuperUser = currentUser.roles?.includes('SUPERUSER');
  }

  private normalizeCoordinates(): void {
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

  myUploader(event: any) {
    for(let file of event.files) {
      this.uploadedFiles.push(file);
    }
  }

  progressReport($event: any) {
    this.primeFileUpload.progress = $event;
  }
}
