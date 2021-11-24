import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'brevet-create-event-dialog',
  templateUrl: './create-event-dialog.component.html',
  styleUrls: ['./create-event-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateEventDialogComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
