import {Component, OnInit, ChangeDetectionStrategy, OnDestroy} from '@angular/core';
import {VolonteerComponentService} from "../volonteer-component.service";
import {map, startWith, switchMap} from "rxjs/operators";
import {SelectItem} from "primeng/api";
import {interval, Subscription} from "rxjs";

@Component({
  selector: 'brevet-checkpoint-selector-listbox',
  templateUrl: './checkpoint-selector-listbox.component.html',
  styleUrls: ['./checkpoint-selector-listbox.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CheckpointSelectorListboxComponent implements OnInit, OnDestroy {

  selectedcheckpoint: unknown [] = [0];

  private intervalSub: Subscription;

  $checkpoints = this.volonteerComponentService.$checkpointsforTrack.pipe(
    map((events) => {
      const items: SelectItem[] = [];
      events.map((event) => {

        if (event.site.place === 'Secret' || event.site.place === 'Secret Checkpoint' || event.site.place === 'Hemlig'){
          items.push( { label: "Hemlig kontroll",value: event.checkpoint_uid})
        } else {
          items.push( { label: event.site.place,value: event.checkpoint_uid})
        }

      });
      return items;
    })
  );

  constructor(private volonteerComponentService :VolonteerComponentService) { }



  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    this.cancelInterval();
  }

  valdKontroll() {

    if (this.intervalSub){
      this.cancelInterval()
      this.reload();
      this.volonteerComponentService.valdkontroll(this.selectedcheckpoint as unknown as string);
    } else {
      this.reload();
      this.volonteerComponentService.valdkontroll(this.selectedcheckpoint as unknown as string);
    }
    // interval(60000).subscribe(x => {
    //   this.volonteerComponentService.valdkontroll(this.selectedcheckpoint as unknown as string)
    // });
    //this.volonteerComponentService.valdkontroll(this.selectedcheckpoint as unknown as string)
  }

  reload() {
    this.intervalSub = interval(60000).pipe(
      startWith(0),
    ).subscribe(data => this.volonteerComponentService.valdkontroll(this.selectedcheckpoint as unknown as string));
  }

  cancelInterval() {
    if (this.intervalSub) {
      this.intervalSub.unsubscribe();
    }
  }
}
