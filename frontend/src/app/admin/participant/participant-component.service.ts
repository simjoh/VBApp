import { Injectable } from '@angular/core';
import {BehaviorSubject, combineLatest, Observable, of, Subject} from "rxjs";
import {
  ParticipantInformationRepresentation,
  ParticipantRepresentation,
  RandonneurCheckPointRepresentation,
  TrackRepresentation
} from "../../shared/api/api";
import {TrackService} from "../../shared/track-service";
import {map, mergeMap, startWith, take} from "rxjs/operators";
import { ParticipantService } from 'src/app/shared/participant.service';

@Injectable()
export class ParticipantComponentService {

  trackSubject = new BehaviorSubject<string>(null);
  track$ = this.trackSubject.asObservable().pipe();

  $currentparticipant = this.participantService.currentParticipant$

  $currentTrackUid = this.trackService.$currentTrack;


  $iscurrentparticipantStarted = this.$currentparticipant.pipe(
    map((val) => val.started === true));

  $ispossibletoSetDnf = this.participantService.possibleToDnf$;
  $ispossibletoSetDns = this.participantService.possibleToDns$;


  $reloadsubject = new Subject();
  $reloadparticipants = this.$reloadsubject.asObservable();


  $participantsfortrack = combineLatest(([this.$reloadparticipants.pipe(startWith('timer start')), this.trackService.$currentTrack])).pipe(
   mergeMap(([checkin ,part]) => {
     if (part === ""){
       return [] as ParticipantInformationRepresentation[];
     }
     return this.participantService.participantsForTrackExtended(part).pipe(
       map((participants) => {
         return participants;
       })
     ) as Observable<ParticipantInformationRepresentation[]>;
   })
    )as Observable<ParticipantInformationRepresentation[]>;


  // $participantsfortrack = this.trackService.$currentTrack.pipe(
  //   mergeMap((val) => {
  //     if (val === ""){
  //         return [] as ParticipantInformationRepresentation[];
  //     }
  //     return this.participantService.participantsForTrackExtended(val).pipe(
  //       map((participants) => {
  //         return participants;
  //       })
  //     ) as Observable<ParticipantInformationRepresentation[]>;
  //   })
  // ) as Observable<ParticipantInformationRepresentation[]>;

  tracks$ = this.trackService.getAllTracks().pipe(
    map((tracks) => {
      return tracks;
    })
  );

  constructor(private trackService: TrackService, private participantService: ParticipantService) {
    this.$reloadsubject.next(null)
  }

  public track(trackuid: string){
    this.trackSubject.next(trackuid);
  }

  public currentTrack(trackUid: string){
    this.trackService.currentTrack(trackUid);
  }

  public setCurrentparticipant(participant: ParticipantRepresentation){
    this.participantService.currentparticipant(participant);
  }

  public isPossibleToRemove(participant: ParticipantRepresentation): boolean {
   return this.participantService.removeLinkExists(participant);
  }

  async remove(participant: ParticipantRepresentation) {
    await this.participantService.deleteParticipant(participant)
  }

  dnf(participant: ParticipantRepresentation) {
    this.participantService.setDnf(participant).then(() => {
      this.$reloadsubject.next("dnf");
    });

  }


  rollbackdnf(participant: ParticipantRepresentation) {
    this.participantService.rollbackDnf(participant).then(() => {
      this.$reloadsubject.next("rollbackdnf");
    });

  }

  dns(participant: any) {
    this.participantService.setDns(participant).then(() => {
      this.$reloadsubject.next("dns");
    });

  }

 async  rollbackDns(participant: ParticipantRepresentation) {
   await  this.participantService.rollbackDns(participant).then(() => {
      this.$reloadsubject.next("rollbackdns");
    });

  }

  reload() {
    this.$reloadsubject.next("reload");
  }

async updateTime(participant: ParticipantRepresentation) {
      await this.participantService.updateTime(participant).then(() => {

      })
  }


    async addbrevenr(participant: ParticipantRepresentation) {
        await this.participantService.addbrevenr(participant).then(() => {

        })
    }
}
