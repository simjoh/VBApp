import { Injectable } from '@angular/core';
import {BehaviorSubject} from "rxjs";
import {CheckpointRepresentation, TrackRepresentation} from "../../api/api";

@Injectable()
export class CheckpointTableComponentService {


  $checkpointsSubject = new BehaviorSubject([] as CheckpointRepresentation[])
  checkpoints$ = this.$checkpointsSubject.asObservable();

  constructor() { }

  initiateCheckpoints(checkpoints: CheckpointRepresentation[]) {
    this.$checkpointsSubject.next(checkpoints)
  }


}
