import { Injectable } from '@angular/core';
import {BehaviorSubject} from "rxjs";
import {TrackRepresentation} from "../../shared/api/api";

@Injectable()
export class ParticipantComponentService {


  trackSubject = new BehaviorSubject<TrackRepresentation>(null);
  track$ = this.trackSubject.asObservable();

  constructor() { }


  public track(trackrepresentation: TrackRepresentation){
    this.trackSubject.next(trackrepresentation);
  }
}
