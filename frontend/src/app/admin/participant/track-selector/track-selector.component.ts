import {Component, OnInit, ChangeDetectionStrategy, Output, EventEmitter, OnDestroy, ChangeDetectorRef} from '@angular/core';
import {map, takeUntil} from "rxjs/operators";
import {SelectItem} from "primeng/api";
import {VolonteerComponentService} from "../../../volunteer/volonteer-component.service";
import {DatePipe} from "@angular/common";
import {ParticipantComponentService} from "../participant-component.service";
import {TrackService} from "../../../shared/track-service";
import {Subject} from "rxjs";

@Component({
  selector: 'brevet-track-selector',
  templateUrl: './track-selector.component.html',
  styleUrls: ['./track-selector.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackSelectorComponent implements OnInit, OnDestroy {

  choosentrack: string | null = null;
  private destroy$ = new Subject<void>();

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

  constructor(
    private participantComponentService: ParticipantComponentService,
    private datePipe: DatePipe,
    private trackService: TrackService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
    // Check current track value immediately
    const currentTrack = this.trackService.getCurrentTrackUid();
    console.log('Track selector init - current track:', currentTrack);
    if (currentTrack) {
      this.choosentrack = currentTrack;
      console.log('Set initial choosentrack to:', this.choosentrack);
    }

    // Listen to the current track from track service and update the dropdown
    this.trackService.$currentTrack.pipe(
      takeUntil(this.destroy$)
    ).subscribe(trackUid => {
      console.log('Track selector received trackUid:', trackUid);
      if (trackUid) {
        this.choosentrack = trackUid;
        console.log('Set choosentrack to:', this.choosentrack);
      } else {
        this.choosentrack = null;
        console.log('Set choosentrack to null');
      }
      this.cdr.detectChanges();
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  valdBana() {
    this.open.emit(this.choosentrack);
    if (this.choosentrack) {
      this.trackService.currentTrack(this.choosentrack);
    }
  }
}
