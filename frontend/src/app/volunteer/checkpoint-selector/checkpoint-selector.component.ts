import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {VolonteerComponentService} from "../volonteer-component.service";
import {map} from "rxjs/operators";
import {SelectItem} from "primeng/api";

@Component({
  selector: 'brevet-checkpoint-selector',
  templateUrl: './checkpoint-selector.component.html',
  styleUrls: ['./checkpoint-selector.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CheckpointSelectorComponent implements OnInit {


  selectedcheckpoint: unknown [] = [0];


  $checkpoints = this.volonteerComponentService.$checkpointsforTrack.pipe(
    map((events) => {
      const items: SelectItem[] = [];
      events.map((event) => {
        items.push( { label: event.site.adress + " " + event.site.place,value: event.checkpoint_uid})
      });
      return items;
    })
  );

  constructor(private volonteerComponentService :VolonteerComponentService) { }

  ngOnInit(): void {
  }

  valdKontroll() {
    this.volonteerComponentService.valdkontroll(this.selectedcheckpoint as unknown as string)
  }
}
