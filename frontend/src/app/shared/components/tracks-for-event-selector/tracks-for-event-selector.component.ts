import {Component, OnInit, ChangeDetectionStrategy, Input, Output, EventEmitter, OnDestroy, ChangeDetectorRef} from '@angular/core';
import { SelectItemGroup } from 'primeng/api';
import {TracksForEventComponentService} from "./tracks-for-event-component.service";
import {map, takeUntil} from "rxjs/operators";
import {Observable, Subject} from "rxjs";
import {TrackService} from "../../track-service";

@Component({
  selector: 'brevet-tracks-for-event-selector',
  templateUrl: './tracks-for-event-selector.component.html',
  styleUrls: ['./tracks-for-event-selector.component.scss'],
  providers: [TracksForEventComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TracksForEventSelectorComponent implements OnInit, OnDestroy {

  @Input() filter: boolean;
  @Input() showclear: boolean;
  @Input() placeholder: string;
  @Input() styleClass: string;
  @Output() trackChange: EventEmitter<any> = new EventEmitter();

  selectedTrack: string;
  selected: [];
  $items = this.trackForEventComponentService.$tracksforEvent
  private destroy$ = new Subject<void>();

  constructor(
    private trackForEventComponentService: TracksForEventComponentService,
    private trackService: TrackService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
    this.trackForEventComponentService.trigger();

    // Listen to the current track from track service and update the dropdown
    this.trackService.$currentTrack.pipe(
      takeUntil(this.destroy$)
    ).subscribe(trackUid => {
      console.log('Tracks-for-event-selector received trackUid:', trackUid);
      if (trackUid) {
        this.selectedTrack = trackUid;
        console.log('Set selectedTrack to:', this.selectedTrack);
      } else {
        this.selectedTrack = null;
        console.log('Set selectedTrack to null');
      }
      this.cdr.detectChanges();
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  setValue($event: any) {
   // this.trackChange.emit(this.selectedTrack);
    this.trackChange.emit($event);
    this.trackForEventComponentService.currentTrack($event)
  }
}
