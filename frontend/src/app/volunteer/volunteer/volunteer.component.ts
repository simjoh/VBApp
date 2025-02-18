import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {Statistics, VolonteerComponentService} from "../volonteer-component.service";
import {map, mergeMap} from "rxjs/operators";
import {ConfirmationService, SelectItem} from 'primeng/api';
import { DatePipe } from '@angular/common';
import {ParticipantToPassCheckpointRepresentation} from "../../shared/api/api";
import {combineLatest} from "rxjs";

@Component({
    selector: 'brevet-volunteer',
    templateUrl: './volunteer.component.html',
    styleUrls: ['./volunteer.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [VolonteerComponentService, ConfirmationService],
    standalone: false
})
export class VolunteerComponent implements OnInit {



  constructor(private vol :VolonteerComponentService, private datePipe: DatePipe, public confirmationService: ConfirmationService) { }

  ngOnInit(): void {
  }


  trackSelected($event: any) {
    this.vol.valdBana($event);
  }
}



