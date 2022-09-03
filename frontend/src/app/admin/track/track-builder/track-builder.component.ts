import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {TrackBuilderComponentService} from "./track-builder-component.service";
import {RusaTimeCalculationApiService} from "./rusa-time-calculation-api.service";
import {RusaTimeAssemblerService} from "./rusa-time-assembler.service";
import {Router} from "@angular/router";
import {BehaviorSubject} from "rxjs";

@Component({
  selector: 'brevet-track-builder',
  templateUrl: './track-builder.component.html',
  styleUrls: ['./track-builder.component.scss'],
  providers: [TrackBuilderComponentService,RusaTimeAssemblerService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackBuilderComponent implements OnInit {

  items = [];
  testa$ = this.test.aktuell;
  value13 = 25;

  editModeSubject = new BehaviorSubject<boolean>(false);
  editMode$ = this.editModeSubject.asObservable();


  constructor(private test: TrackBuilderComponentService,private router: Router) { }

  ngOnInit(): void {

    this.items = [
      {label: 'Step 1'},
      {label: 'Step 2'},
      {label: 'Step 3'}
    ];
     // this.test.read();
  }

  setMode() {
    // this.router.navigate(['/admin/clubadmin'])
    this.editModeSubject.next(!this.editModeSubject.value)
  }
}
