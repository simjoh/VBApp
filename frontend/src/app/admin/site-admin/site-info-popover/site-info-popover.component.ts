import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import { Site } from 'src/app/shared/api/api';

@Component({
  selector: 'brevet-site-info-popover',
  templateUrl: './site-info-popover.component.html',
  styleUrls: ['./site-info-popover.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class SiteInfoPopoverComponent implements OnInit {

  @Input() site : Site

  constructor() { }

  ngOnInit(): void {
  }

  getDistanceInMeters(distance: string): string {
    const meters = parseFloat(distance) * 1000;
    return `${Math.round(meters)} m`;
  }

}
