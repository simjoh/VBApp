import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService} from "primeng/dynamicdialog";
import {ConfirmationService} from "primeng/api";
import {map} from "rxjs/operators";
import {Observable} from "rxjs";
import {DeviceDetectorService} from "ngx-device-detector";
import {CreateSiteDialogComponent} from "../create-site-dialog/create-site-dialog.component";
import { Site } from 'src/app/shared/api/api';
import { SiteService } from '../site.service';


@Component({
  selector: 'brevet-site-list',
  templateUrl: './site-list.component.html',
  styleUrls: ['./site-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers:[DialogService, ConfirmationService]
})
export class SiteListComponent implements OnInit {



  $sites = this.siteService.siteWithAdd$.pipe(
    map((s:Array<Site>) => {
      // this.table. = 0;
      return s;
    })
  ) as Observable<Site[]>;

  constructor(private siteService: SiteService,
              private dialogService: DialogService,
              private confirmationService: ConfirmationService,
              private deviceDetector: DeviceDetectorService) { }



  ngOnInit(): void {
  }

  openNew() {
    let width;
    if ( this.deviceDetector.isDesktop()){
      width = "30%";
    } else {
      width = "80%"
    }

    const ref = this.dialogService.open(CreateSiteDialogComponent, {
      data: {
        id: '51gF3'
      },
      header: 'Lägg till Site',
    });

    ref.onClose.subscribe((event: Site) => {
      if (event) {
        this.siteService.newSite(event);
      }
    });
  }

  editProduct(user_uid: any) {

  }

  deleteProduct(site_uid: any) {
    this.confirmationService.confirm({
      message: 'Are you sure you want to delete ' + site_uid + '?',
      header: 'Confirm',
      icon: 'pi pi-exclamation-triangle',
      accept: () => {
        console.log(site_uid)
        this.siteService.deleteSite(site_uid);
      },
      reject: () => {
        console.log("reject");
      }
    });
  }
}
