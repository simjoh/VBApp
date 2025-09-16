import { Component, OnInit, ChangeDetectionStrategy, OnDestroy } from '@angular/core';
import {TrackBuilderComponentService} from "./track-builder-component.service";
import {RusaTimeCalculationApiService} from "./rusa-time-calculation-api.service";
import {RusaTimeAssemblerService} from "./rusa-time-assembler.service";
import {Router, ActivatedRoute, NavigationEnd} from "@angular/router";
import {BehaviorSubject, Subscription} from "rxjs";
import { filter } from 'rxjs/operators';

@Component({
  selector: 'brevet-track-builder',
  templateUrl: './track-builder.component.html',
  styleUrls: ['./track-builder.component.scss'],
  providers: [TrackBuilderComponentService,RusaTimeAssemblerService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackBuilderComponent implements OnInit, OnDestroy {

  items = [];
  testa$ = this.test.aktuell;
  value13 = 25;

  editModeSubject = new BehaviorSubject<boolean>(false);
  editMode$ = this.editModeSubject.asObservable();

  currentMode: 'create' | 'gpx' | 'copy' = 'create';
  private routerSubscription: Subscription;

  constructor(
    private test: TrackBuilderComponentService,
    private router: Router,
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    // Check for mode parameter in URL
    this.route.queryParams.subscribe(params => {
      const mode = params['mode'];
      if (mode === 'gpx') {
        // Show coming soon alert and go back
        alert('Denna funktion kommer snart!');
        this.router.navigate(['/admin/banor']);
        return;
      } else if (mode === 'copy') {
        // Show coming soon alert and go back
        alert('Denna funktion kommer snart!');
        this.router.navigate(['/admin/banor']);
        return;
      } else {
        this.currentMode = 'create';
        this.editModeSubject.next(true); // Go directly to form for create
      }
    });

    this.items = [
      {label: 'Step 1'},
      {label: 'Step 2'},
      {label: 'Step 3'}
    ];
     // this.test.read();
  }

  ngOnDestroy(): void {
    if (this.routerSubscription) {
      this.routerSubscription.unsubscribe();
    }
  }

  setMode(mode: 'create' | 'gpx' | 'copy') {
    this.currentMode = mode;
    if (mode === 'gpx' || mode === 'copy') {
      // Show coming soon message for these modes
      alert('Denna funktion kommer snart!');
      return;
    }
    if (mode === 'create') {
      this.editModeSubject.next(true);
    }
  }

  goBack() {
    this.editModeSubject.next(false);
    this.currentMode = 'create';
  }
}
