import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
    selector: 'brevet-permission-admin',
    templateUrl: './permission-admin.component.html',
    styleUrls: ['./permission-admin.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class PermissionAdminComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
