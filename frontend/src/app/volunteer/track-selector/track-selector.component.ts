import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {VolonteerComponentService} from "../volonteer-component.service";
import {map} from "rxjs/operators";
import {SelectItem} from "primeng/api";
import {DatePipe} from "@angular/common";

@Component({
  selector: 'brevet-track-selector',
  templateUrl: './track-selector.component.html',
  styleUrls: ['./track-selector.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackSelectorComponent implements OnInit {

  choosentrack: unknown [] = [0];

  $tracks = this.volonteerComponentService.$tracksforevent.pipe(
    map((trackarray: any) => {
      const tracks: SelectItem[] = [];
      trackarray.map((track) => {
        tracks.push( { label: track.title + ' ' + this.datePipe.transform(track.start_date_time.replace(' ', 'T'), 'yyyy-MM-dd') , value :track.track_uid});
      //  tracks.push( { label: track.title + ' ', value :track.track_uid});
      });
      return tracks;
    })
  );

  constructor(private volonteerComponentService :VolonteerComponentService,private datePipe: DatePipe) { }

  ngOnInit(): void {
  }

  valdBana() {
    this.volonteerComponentService.valdBana(this.choosentrack as unknown as string);
  }
}
