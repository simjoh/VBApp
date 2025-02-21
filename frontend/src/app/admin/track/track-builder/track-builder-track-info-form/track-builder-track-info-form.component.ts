import { Component, OnInit, ChangeDetectionStrategy, Output, EventEmitter } from '@angular/core';
import { TrackBuilderComponentService } from "../track-builder-component.service";
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CardModule } from 'primeng/card';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { InputNumberModule } from 'primeng/inputnumber';
import { CalendarModule } from 'primeng/calendar';
import { TooltipModule } from 'primeng/tooltip';
import { DropdownModule } from 'primeng/dropdown';
import { EventSelectorComponent } from '../../../../shared/components/event-selector/event-selector.component';

@Component({
  selector: 'brevet-track-builder-track-info-form',
  templateUrl: './track-builder-track-info-form.component.html',
  styleUrls: ['./track-builder-track-info-form.component.scss'],
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    CardModule,
    ButtonModule,
    InputTextModule,
    InputNumberModule,
    CalendarModule,
    TooltipModule,
    DropdownModule,
    EventSelectorComponent
  ]
})
export class TrackBuilderTrackInfoFormComponent implements OnInit {
  model = new EventTrackInfo(0, "", "", "", "", "");
  saving = false;

  @Output() goToControls = new EventEmitter<void>();

  constructor(
    private trackbuildercomponentService: TrackBuilderComponentService,
    private router: Router
  ) { }

  ngOnInit(): void {
  }

  addEvent($event: any) {
    this.model.event_uid = $event;
    this.trackbuildercomponentService.choosenEvent(this.model.event_uid);
  }

  add() {
    this.trackbuildercomponentService.rusaInput(
      {
        event_distance: this.model.trackdistance,
        start_time: this.model.starttime,
        start_date: this.model.startdate,
        event_uid: "",
        track_title: this.model.trackname,
        controls: [],
        link: this.model.link
      }
    )
  }

  saveTrack() {
    if (this.saving) return;

    this.saving = true;
    this.trackbuildercomponentService.saveTrack(this.model).subscribe({
      next: (result) => {
        console.log('Track saved successfully:', result);
        if (!this.model.id) {
          this.model.id = result.track_uid;
        }
        this.saving = false;
      },
      error: (error) => {
        console.error('Error saving track:', error);
        this.saving = false;
      }
    });
  }

  saveControls() {
    this.goToControls.emit();
  }

  cancel() {
    this.router.navigate(['/tracks']); // Adjust the route as needed
  }

  openStravaLink() {
    if (this.model.link) {
      window.open(this.model.link, '_blank');
    }
  }
}

export class EventTrackInfo {
  id?: number;

  constructor(
    public trackdistance: number,
    public trackname: string,
    public event_uid: string,
    public starttime?: string,
    public startdate?: string,
    public link?: string,
  ) { }
}
