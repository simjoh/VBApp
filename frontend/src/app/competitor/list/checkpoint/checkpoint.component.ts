import {Component, OnInit, ChangeDetectionStrategy, Input, Output, EventEmitter} from '@angular/core';
import {CompetitorListComponentService} from "../competitor-list-component.service";
import {RandonneurCheckPointRepresentation} from "../../../shared/api/api";
import {BehaviorSubject} from "rxjs";
import {LinkService} from "../../../core/link.service";
import {map} from "rxjs/operators";
import { ConfirmationService } from 'primeng/api';
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
      if (val === false){
        this.checkinknapptext = 'Checka in'
      } else {
        this.checkinknapptext = 'Ångra checka in'
      }
      return val;
    })
  );

  dnfSubject = new BehaviorSubject(false);
  isdnf$ = this.dnfSubject.asObservable().pipe(
    map((val) => {
      if (val === true){
        this.dnfknapptext = 'Ångra DNF'
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

  @Output() checkedin = new EventEmitter();
  @Output() dnf = new EventEmitter();

  constructor(private linkservice: LinkService, private confirmationService: ConfirmationService) { }

  ngOnInit(): void {
    this.chekedinSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp') === false)
    this.dnfSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.dnf') === false)

    console.log(this.distancetonext)
  }
  checkin() {

       if (!this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp')){

         this.confirmationService.confirm({
           message: 'Are you sure that you want to undo checkin at  '  + this.checkpoints.checkpoint.site.adress + " " + this.checkpoints.checkpoint.site.place,
           accept: () => {
             this.checkedin.emit(false)
             this.chekedinSubject.next(false);
           }
         });
       } else {
         this.confirmationService.confirm({
           message: 'Are you sure that you want to checkin at ' + this.checkpoints.checkpoint.site.adress + " " + this.checkpoints.checkpoint.site.place,
           accept: () => {
             this.checkedin.emit(false)
             this.checkedin.emit(true)
             this.chekedinSubject.next(true);
           }
         });
       }
  }

  setdnf(){
    if (this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.dnf')){
      this.dnfSubject.next(true);
      this.dnf.emit(true);
    } else {
      this.dnfSubject.next(false);
      this.dnf.emit(false);
    }
  }



}
