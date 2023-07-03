import {ChangeDetectionStrategy, Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {RandonneurCheckPointRepresentation} from "../../../shared/api/api";
import {BehaviorSubject} from "rxjs";
import {LinkService} from "../../../core/link.service";
import {map} from "rxjs/operators";
import {ConfirmationService} from 'primeng/api';

@Component({
  selector: 'brevet-checkpoint',
  templateUrl: './checkpoint.component.html',
  styleUrls: ['./checkpoint.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [ConfirmationService]
})
export class CheckpointComponent implements OnInit {


  checkinknapptext: string;
  dnfknapptext: string

  chekedinSubject = new BehaviorSubject(false);
  checkedin$ = this.chekedinSubject.asObservable().pipe(
    map((val) => {
      if (val === false) {
        this.checkinknapptext = 'CHECK IN'
      } else {
        this.checkinknapptext = 'UNDO CHECK IN'
      }
      return val;
    })
  );

  dnfSubject = new BehaviorSubject(false);
  isdnf$ = this.dnfSubject.asObservable().pipe(
    map((val) => {
      if (val === true) {
        this.dnfknapptext = 'UNDO DNF'
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
  @Input() preview: boolean
  @Input() nextIsSecret: boolean


  @Output() checkedin = new EventEmitter<any>();
  @Output() dnf = new EventEmitter<any>();

  constructor(private linkservice: LinkService, private confirmationService: ConfirmationService) {
  }


  ngOnInit(): void {

    if (this.preview === true) {
      this.chekedinSubject.next(false)
      this.dnfSubject.next(false)
    } else {
      this.chekedinSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp') === false)
      this.dnfSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.dnf') === false)
    }

  }

  checkin() {

    if (this.preview === true) {
      alert("Preview mode");
    }

    if (!this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp')) {

      this.confirmationService.confirm({
        message: 'Do you want want to undo check in',
        accept: () => {
          this.checkedin.emit(false);
          this.chekedinSubject.next(false);
        }
      });
    } else {
      this.confirmationService.confirm({
        message: 'Do you want want to check in',
        accept: () => {
          this.checkedin.emit(true);
        }
      });
    }
  }

  setdnf() {

    if (this.preview === true) {
      alert("Preview mode");
    }
    if (this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.dnf')) {
      //  this.dnfSubject.next(true);
      this.dnf.emit(true);
    } else {
      // this.dnfSubject.next(false);
      this.dnf.emit(false);
    }
  }


  trimWhitespaces(s: string) {
    return s.replace('/ /gi', "")
  }


}
