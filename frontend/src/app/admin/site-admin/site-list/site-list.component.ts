import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService} from "primeng/dynamicdialog";
import {ConfirmationService} from "primeng/api";
import {map, take} from "rxjs/operators";
import {Observable} from "rxjs";
import {DeviceDetectorService} from "ngx-device-detector";
import {CreateSiteDialogComponent} from "../create-site-dialog/create-site-dialog.component";
import {Site, SiteRepresentation, User} from 'src/app/shared/api/api';
import { SiteService } from '../site.service';
import {LinkService} from "../../../core/link.service";
import {CreateUserDialogComponent} from "../../user-admin/create-user-dialog/create-user-dialog.component";
import {EditSiteDialogComponent} from "../edit-site-dialog/edit-site-dialog.component";


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
      return s;
    })
  ) as Observable<Site[]>;

  constructor(private siteService: SiteService, private linkService: LinkService,
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

  editProduct(site: string) {

    const editref = this.dialogService.open(EditSiteDialogComponent, {
      data: {
        user: site,
        id: '51gF3'
      },
      header: 'Editera kontrollplats',
    });

    editref.onClose.pipe(take(1)).subscribe(((user: SiteRepresentation) => {
      if (user) {
        console.log(user);
        this.siteService.updateUser(user);
      } else {
        editref.destroy();
      }

    }));

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

  canDelete(site: any):boolean {
    return this.linkService.exists(site.links,"relation.site.delete");
  }
}
