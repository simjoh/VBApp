import { Component, ChangeDetectionStrategy } from '@angular/core';
import {CompetitorListComponentService} from "./competitor-list-component.service";

@Component({
  selector: 'brevet-list',
  templateUrl: './list.component.html',
  styleUrls: ['./list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [CompetitorListComponentService]
})
export class ListComponent {


  checkpoints$ = this.comp.$controls;

  $track = this.comp.$track;

  constructor(private comp: CompetitorListComponentService) { }



}
