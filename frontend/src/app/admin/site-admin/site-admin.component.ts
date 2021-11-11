import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'brevet-site-admin',
  templateUrl: './site-admin.component.html',
  styleUrls: ['./site-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class SiteAdminComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
