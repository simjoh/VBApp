
import { ParticipantService } from 'src/app/shared/participant.service';
import {map, mergeMap, startWith, switchMap, withLatestFrom} from "rxjs/operators";
import {CheckpointRepresentation, ParticipantRepresentation, RandonneurCheckPointRepresentation} from "../../../shared/api/api";
import {BehaviorSubject, combineLatest, interval, Observable, Subject, Subscription} from "rxjs";
import {Injectable} from "@angular/core";
import {ParticipantComponentService} from "../participant-component.service";

@Injectable()
export class ParticipantCheckpointTableComponentService {

  private intervalSub: Subscription;

  $participantSubject = new BehaviorSubject(null);
  $participant = this.$participantSubject.asObservable();

  $checkinSubject = new BehaviorSubject(null);
  $checkedin = this.$checkinSubject.asObservable();


  $checkpoints = combineLatest(([this.$checkedin, this.$participant])).pipe(
    mergeMap(([checkin ,part]) => {
      if (part === null) {
        return [];
      }
      return this.participantService.checkpointsForparticipant(part).pipe(
        map((checkpoints: RandonneurCheckPointRepresentation[]) => {
          return checkpoints;
        })
      );
    })
  ) as Observable<RandonneurCheckPointRepresentation[]>;

  $dimCheckin = this.$participant.pipe(
    map((part:ParticipantRepresentation) => {
      if(part.dnf === true || part.dns === true){
        return true
      } else {
        return false;
      }
    })
  );


  constructor(private participantService: ParticipantService,private  para: ParticipantComponentService) { }


  initCheckpoints(participant : ParticipantRepresentation){
      this.$participantSubject.next(participant);
  }

  async checkin(checkpoint: RandonneurCheckPointRepresentation){
    this.participantService.stamplinkExists(checkpoint).then((res) => {
      if (res === true){
         this.participantService.checkinAdmin(checkpoint).then((res) => {
           this.para.reload();
          this.$checkinSubject.next(true);
        });
      } else {
        this.rollbackStamp(checkpoint);
      }
    });
  }



  async rollbackStamp(checkpoint: RandonneurCheckPointRepresentation) {
    await this.participantService.rollbackcheckinAdmin(checkpoint).then((res) => {
      this.para.reload();
      this.$checkinSubject.next(false);
    });
  }
}
