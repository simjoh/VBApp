import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
    selector: 'brevet-admin',
    templateUrl: './admin.component.html',
    styleUrls: ['./admin.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class AdminComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
