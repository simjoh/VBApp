import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
    selector: 'brevet-admin-start',
    templateUrl: './admin-start.component.html',
    styleUrls: ['./admin-start.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class AdminStartComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
