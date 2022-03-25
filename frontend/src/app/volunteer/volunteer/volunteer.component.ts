import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {Statistics, VolonteerComponentService} from "../volonteer-component.service";
import {map, mergeMap} from "rxjs/operators";
import { SelectItem } from 'primeng/api';
import { DatePipe } from '@angular/common';
import {ParticipantToPassCheckpointRepresentation} from "../../shared/api/api";
import {combineLatest} from "rxjs";

@Component({
  selector: 'brevet-volunteer',
  templateUrl: './volunteer.component.html',
  styleUrls: ['./volunteer.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [VolonteerComponentService]
})
export class VolunteerComponent implements OnInit {

  ss = new VyInformation();
  choosen: unknown [] = [0];
  choosentrack: unknown [] = [0];
  hoosencheckpoint: unknown [] = [0];
  $valtEvent = this.vol.vald$
  $valdbana = this.vol.valdbana$;

  value6: number = 60;
  $valdKontroll = this.vol.valdkontroll$.pipe(
    map((ss) => {
      this.ss.choosenControl = ss.site.adress + " " + ss.site.place;
    })
  )

  randonneurs = combineLatest([this.vol.valdkontroll$, this.vol.randonneurs$, this.vol.stats$]).pipe(
    map(([all, insert, mesure]) =>  {
          this.ss = new VyInformation();
          this.ss.randonnerurs = insert;
          this.ss.choosenControl = all.site.adress + " " + all.site.place;
          this.ss.statistics = mesure;
          return this.ss;
    }),
  );





  // randonneurs = this.vol.randonneurs$.pipe(
  //   map((s) => {
  //     this.ss.randonnerurs = s;
  //     return this.ss;
  //   })
  // );

  products: Product[] = [];

 $test = this.vol.$tracksforevent.pipe(
   map((trackarray: any) => {
     const tracks: SelectItem[] = [];
     trackarray.map((track) => {
       tracks.push( { label: track.title + ' ' + this.datePipe.transform(track.start_date_time, 'yyyy-MM-dd') , value :track.track_uid});
     });
     return tracks;
   })
 );

  $eventItems = this.vol.$allEvents.pipe(
    map((events) => {
      const items: SelectItem[] = [];
      events.map((event) => {
        items.push( { label: event.title,value: event.event_uid})
      });
      return items;
    })
  );

  $checkpoints = this.vol.$checkpointsforTrack.pipe(
    map((events) => {
      const items: SelectItem[] = [];
      events.map((event) => {
        items.push( { label: event.site.adress + " " + event.site.place,value: event.checkpoint_uid})
      });
      return items;
    })
  );



  constructor(private vol :VolonteerComponentService, private datePipe: DatePipe) { }

  ngOnInit(): void {
  }

  valt() {

    this.vol.valdBana(null);
    this.vol.valdkontroll(null)
    this.vol.valtEvent(this.choosen as unknown as string);
  }

  valdBana() {
    this.vol.valdkontroll(null)
    this.vol.valdBana(this.choosentrack as unknown as string);
  }

  valdKontroll() {

    // const timer: ReturnType<typeof setInterval> = setInterval(() => {
    //   console.log("sssssssssssss");
    //
    // }, 2000);

    this.vol.valdkontroll(this.hoosencheckpoint as unknown as string)



  }
}

export interface Product {
  name: string;
  startnumber;
}

export class VyInformation {
  statistics: Statistics;
  randonnerurs: ParticipantToPassCheckpointRepresentation[];
  choosenControl: string;
  choosen: unknown [] = [0];
  choosentrack: unknown [] = [0];
  hoosencheckpoint: unknown [] = [0];
}


