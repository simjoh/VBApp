import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import { User } from 'src/app/shared/api/api';

@Component({
    selector: 'brevet-user-info-popover',
    templateUrl: './user-info-popover.component.html',
    styleUrls: ['./user-info-popover.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class UserInfoPopoverComponent implements OnInit {

  constructor() { }

  @Input() user : User

  ngOnInit(): void {
  }

}
