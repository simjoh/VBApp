import { Injectable } from '@angular/core';
import {VolonteerService} from "./volonteer.service";
import {EventService} from "../admin/event-admin/event.service";
import {BehaviorSubject, Observable} from "rxjs";
import {map, mergeMap, startWith, take, tap, withLatestFrom} from "rxjs/operators";
import {TrackService} from "../shared/track-service";
import {CheckpointRepresentation, ParticipantToPassCheckpointRepresentation} from "../shared/api/api";

@Injectable()
export class VolonteerComponentService {

  $allEvents = this.eventService.allEvents$

  valtEventSubject = new BehaviorSubject<string>(null);
  valtBanaSubject = new BehaviorSubject<string>(null);
  valtkontrollSubject = new BehaviorSubject<string>(null);


  vald$ =  this.valtEventSubject.asObservable().pipe(
    withLatestFrom(this.$allEvents),
    map(([first, second]) => {
     const val = second.find(item => {
        return item.event_uid === first;
      })
      return val;
    }),
  );

  $tracksforevent = this.vald$.pipe(
    mergeMap((vald: any) => {
       return this.trackService.tracksForEvent(vald.event_uid);
    }),
    map((tracks => {

      return tracks;
    }))
  );


  // $tracksforevent2 = this.valtBanaSubject.asObservable().pipe(
  //   map((vald: any) => {
  //     return this.trackService.getAllTracks();
  //   }),
  //   map((tracks => {
  //
  //     return tracks;
  //   }))
  // ).subscribe();


  // valdbana$ =  this.valtBanaSubject.asObservable().pipe(
  //   withLatestFrom(this.$tracksforevent),
  //   map(([first, second]) => {
  //     const valdbana = second.find(item => {
  //       return first === item.track_uid;
  //     })
  //     return valdbana;
  //   }),
  // ) as Observable<any>;


  valdbana$ =  this.valtBanaSubject.asObservable().pipe(
    withLatestFrom(this.trackService.getAllTracks()),
    map(([first, second]) => {
      const valdbana = second.find(item => {
        return first === item.track_uid;
      })
      return valdbana;
    }),
  ) as Observable<any>;




  $checkpointsforTrack = this.valdbana$.pipe(
    mergeMap((vald: any) => {
      return this.volonteerService.getCheckpointsForTrack(vald.track_uid);
    }),
    map((checkpoints => {
      return checkpoints;
    }))
  );

  valdkontroll$ =  this.valtkontrollSubject.asObservable().pipe(
    withLatestFrom(this.$checkpointsforTrack),
    map(([first, second]) => {
      const valdbana = second.find(item => {

        return first === item.checkpoint_uid;
      })
      console.log(valdbana);
      return valdbana;
    }),
  ) as Observable<CheckpointRepresentation>;


  randonneurs$ =  this.valdkontroll$.pipe(
    withLatestFrom(this.valdbana$),
    mergeMap(([first, second]) => {
      return this.volonteerService.getCheckpoints(second.track_uid, first.checkpoint_uid)
    }),
    map((checkpoints) => {
      return  checkpoints;
    })
  ) as Observable<ParticipantToPassCheckpointRepresentation[]>;


  stats$ = this.randonneurs$.pipe(
    map((rand) => {
      const stats = new Statistics();
      stats.countpassed = rand.filter((obj) => obj.passed === true).length;
      stats.notPassed = rand.filter((obj) => obj.passed === false).length;
      stats.dnf = rand.filter((obj) => obj.dnf === true).length;
      if (!stats.dns){
          stats.dns = 0;
      }
      if (!stats.dnf){
        stats.dnf = 0
      }
      const dnfochdns = stats.dns + stats.dnf;
      stats.percentageoff = stats.countpassed - dnfochdns / rand.length * 100;
      stats.percentageoff = Math.floor((100 * stats.countpassed - dnfochdns) / rand.length);
      console.log(stats.percentageoff);
      if (stats.percentageoff < 0){
        stats.percentageoff = 0;
      }
      return stats;

    })
  ) as Observable<Statistics>;



  constructor(private volonteerService: VolonteerService,private   eventService: EventService, private trackService: TrackService) { }

  public valtEvent(valt: string) {
    this.valtEventSubject.next(valt)
  }

  public valdBana(valdbana: string){
    this.valtBanaSubject.next(valdbana);
  }

  valdkontroll(valdkontroll: string) {
   this.valtkontrollSubject.next(valdkontroll);
  }

  async checkin(product: any) {
    await this.volonteerService.checkinParticipant(product).then((status) => {
    this.valdkontroll(product.checkpointUid);
  });
  }

  async rollbackCheckin(product: any) {
    await this.volonteerService.rollbackParticipantCheckin(product).then((status) => {
      this.valdkontroll(product.checkpointUid);
    });
  }

  async rollbackDnf(product: any) {
    await this.volonteerService.rollbackDnf(product).then((status) => {
      this.valdkontroll(product.checkpointUid);
    });
  }

  async setDnf(product: any) {
    await this.volonteerService.setDnf(product).then((status) => {
      this.valdkontroll(product.checkpointUid);
    });
  }
}


export class Statistics {
  countpassed: number;
  notPassed: number;
  dnf: number;
  dns: number;
  percentageoff: number
}
