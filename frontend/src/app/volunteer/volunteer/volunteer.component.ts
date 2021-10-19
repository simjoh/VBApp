import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'brevet-volunteer',
  templateUrl: './volunteer.component.html',
  styleUrls: ['./volunteer.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class VolunteerComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
