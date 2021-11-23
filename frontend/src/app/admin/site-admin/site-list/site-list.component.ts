import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {DialogService} from "primeng/dynamicdialog";
import {ConfirmationService} from "primeng/api";
import {SiteService} from "../site.service";
import {map} from "rxjs/operators";
import {Site, User} from "../../../shared/api/api";
import {Observable} from "rxjs";

@Component({
  selector: 'brevet-site-list',
  templateUrl: './site-list.component.html',
  styleUrls: ['./site-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers:[DialogService, ConfirmationService]
})
export class SiteListComponent implements OnInit {


  $sites = this.siteService.allSites$.pipe(
    map((s:Array<Site>) => {
      // this.table. = 0;
      return s;
    })
  ) as Observable<Site[]>;

  constructor(private siteService: SiteService) { }



  ngOnInit(): void {
  }

  openNew() {

  }

  editProduct(user_uid: any) {

  }

  deleteProduct(user_uid: any) {

  }
}
