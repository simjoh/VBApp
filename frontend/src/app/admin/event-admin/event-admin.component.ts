import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'brevet-event-admin',
  templateUrl: './event-admin.component.html',
  styleUrls: ['./event-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EventAdminComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
