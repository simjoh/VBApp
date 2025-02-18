import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
    selector: 'brevet-unknown-route',
    templateUrl: './unknown-route.component.html',
    styleUrls: ['./unknown-route.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class UnknownRouteComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
