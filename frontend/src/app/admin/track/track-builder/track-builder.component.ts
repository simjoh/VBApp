import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'brevet-track-builder',
  templateUrl: './track-builder.component.html',
  styleUrls: ['./track-builder.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackBuilderComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
