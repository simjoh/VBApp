import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'brevet-create-site-dialog',
  templateUrl: './create-site-dialog.component.html',
  styleUrls: ['./create-site-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateSiteDialogComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
