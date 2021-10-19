import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'brevet-user-admin',
  templateUrl: './user-admin.component.html',
  styleUrls: ['./user-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UserAdminComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
