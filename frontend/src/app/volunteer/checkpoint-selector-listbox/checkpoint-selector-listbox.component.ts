import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {VolonteerComponentService} from "../volonteer-component.service";
import {map} from "rxjs/operators";
import {SelectItem} from "primeng/api";

@Component({
  selector: 'brevet-checkpoint-selector-listbox',
  templateUrl: './checkpoint-selector-listbox.component.html',
  styleUrls: ['./checkpoint-selector-listbox.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CheckpointSelectorListboxComponent implements OnInit {

  selectedcheckpoint: unknown [] = [0];

  $checkpoints = this.volonteerComponentService.$checkpointsforTrack.pipe(
    map((events) => {
      const items: SelectItem[] = [];
      events.map((event) => {
        items.push( { label: event.site.adress + " - " + event.site.place,value: event.checkpoint_uid})
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
