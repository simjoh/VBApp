import {Component, OnInit, ChangeDetectionStrategy, Output, EventEmitter} from '@angular/core';
import {map} from "rxjs/operators";
import {SelectItem} from "primeng/api";
import {VolonteerComponentService} from "../../../volunteer/volonteer-component.service";
import {DatePipe} from "@angular/common";
import {ParticipantComponentService} from "../participant-component.service";

@Component({
  selector: 'brevet-track-selector',
  templateUrl: './track-selector.component.html',
  styleUrls: ['./track-selector.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackSelectorComponent implements OnInit {

  choosentrack: unknown [] = [0];



  @Output() open: EventEmitter<any> = new EventEmitter();

  $tracks = this.participantComponentService.tracks$.pipe(
    map((trackarray: any) => {
      const tracks: SelectItem[] = [];
      trackarray.map((track) => {
        tracks.push( { label: track.title + ' ' + this.datePipe.transform(track.start_date_time.replace(' ', 'T')) , value :track.track_uid});
      });
      return tracks;
    })
  );

  constructor(private participantComponentService :ParticipantComponentService,private datePipe: DatePipe) { }

  ngOnInit(): void {
  }

  valdBana() {
    this.open.emit(this.choosentrack);
    this.participantComponentService.track(this.choosentrack as unknown as string);
  }
}
