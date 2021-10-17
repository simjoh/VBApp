import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'brevet-competitor',
  templateUrl: './competitor.component.html',
  styleUrls: ['./competitor.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CompetitorComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
  }

}
