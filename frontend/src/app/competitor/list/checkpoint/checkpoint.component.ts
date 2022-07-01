import {Component, OnInit, ChangeDetectionStrategy, Input, Output, EventEmitter, OnChanges, SimpleChanges} from '@angular/core';
import {CompetitorListComponentService} from "../competitor-list-component.service";
import {RandonneurCheckPointRepresentation} from "../../../shared/api/api";
import {BehaviorSubject} from "rxjs";
import {LinkService} from "../../../core/link.service";
import {map, sample} from "rxjs/operators";
import {ConfirmationService} from 'primeng/api';
import {inputNames} from "@angular/cdk/schematics";

@Component({
  selector: 'brevet-checkpoint',
  templateUrl: './checkpoint.component.html',
  styleUrls: ['./checkpoint.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [CompetitorListComponentService, ConfirmationService]
})
export class CheckpointComponent implements OnInit {


  checkinknapptext: string;
  dnfknapptext: string

  chekedinSubject = new BehaviorSubject(false);
  checkedin$ = this.chekedinSubject.asObservable().pipe(
    map((val) => {
      if (val === false) {
        this.checkinknapptext = 'Checkin'
      } else {
        this.checkinknapptext = 'Undo checkin'
      }
      return val;
    })
  );

  dnfSubject = new BehaviorSubject(false);
  isdnf$ = this.dnfSubject.asObservable().pipe(
    map((val) => {
      if (val === true) {
        this.dnfknapptext = 'Undo DNF'
      } else {
        this.dnfknapptext = 'DNF'
      }
      return val;
    })
  );

  @Input() checkpoints: RandonneurCheckPointRepresentation
  @Input() start: boolean
  @Input() finsih: boolean
  @Input() distance: any
  @Input() distancetonext: any

  @Output() checkedin = new EventEmitter<any>();
  @Output() dnf = new EventEmitter<any>();

  constructor(private linkservice: LinkService, private confirmationService: ConfirmationService) {
  }


  ngOnInit(): void {
    this.chekedinSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp') === false)
    this.dnfSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.dnf') === false)
  }
  checkin() {

       if (!this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp')){

         this.confirmationService.confirm({
           message: 'Are you sure that you want to undo checkin at  '  + this.checkpoints.checkpoint.site.adress + " " + this.checkpoints.checkpoint.site.place,
           accept: () => {
             this.checkedin.emit(false);
             this.chekedinSubject.next(false);
           }
         });
       } else {
         this.confirmationService.confirm({
           message: 'Are you sure that you want to checkin at ' + this.checkpoints.checkpoint.site.adress + " " + this.checkpoints.checkpoint.site.place,
           accept: () => {
             this.checkedin.emit(true);
           }
         });
       }
  }

  setdnf(){
    if (this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.dnf')){
    //  this.dnfSubject.next(true);
      this.dnf.emit(true);
    } else {
     // this.dnfSubject.next(false);
      this.dnf.emit(false);
    }
  }



}
