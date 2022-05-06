import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {Statistics, VolonteerComponentService} from "../volonteer-component.service";
import {ParticipantToPassCheckpointRepresentation} from "../../shared/api/api";
import {BehaviorSubject, combineLatest} from "rxjs";
import {map} from "rxjs/operators";
import {LinkService} from "../../core/link.service";
import {ConfirmationService} from "primeng/api";

@Component({
  selector: 'brevet-participant-list',
  templateUrl: './participant-list.component.html',
  styleUrls: ['./participant-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: []
})
export class ParticipantListComponent implements OnInit {

  ss = new VyInformation();

  checkinknapptext: string;
  dnfknapptext: string

  chekedinSubject = new BehaviorSubject(false);
  checkedin$ = this.chekedinSubject.asObservable().pipe(
    map((val) => {
      let text = "";
      if (val === false){
        text = 'Checka in'
      } else {
        text = 'Ångra'
      }
      return text;
    })
  );

  dnfSubject = new BehaviorSubject(false);
  isdnf$ = this.dnfSubject.asObservable().pipe(
    map((val) => {
      let text = "";
      if (val === false){
        text = 'Ångra'
      } else {
        text = 'DNF'
      }
      return text;
    })
  );

  randonneurs = combineLatest([this.volonteerComponentService.valdkontroll$, this.volonteerComponentService.randonneurs$, this.volonteerComponentService.stats$]).pipe(
    map(([all, insert, mesure]) =>  {
      this.ss = new VyInformation();
      this.ss.randonnerurs = insert;
      this.ss.choosenControl = all.site.adress + " " + all.site.place;
      this.ss.statistics = mesure;
      return this.ss;
    }),
  );


  constructor(private volonteerComponentService :VolonteerComponentService,
              private linkservice: LinkService,
              public confirmationService: ConfirmationService) { }

  ngOnInit(): void {

  }

  async checkin(product: any,event: Event) {

    if (!this.linkservice.exists(product.link, 'relation.volonteer.stamp')) {
      this.volonteerComponentService.rollbackCheckin(product);
      this.chekedinSubject.next(false);
     // await this.confirmationService.confirm({
     //    target: event.target,
     //    message: 'Är du säker på att du vill ångra incheckning av ' + product.startNumber +
     //      '   ' + product.givenName + ' ' + product.familyName,
     //    icon: 'pi pi-exclamation-triangle',
     //    accept: () => {
     //      this.volonteerComponentService.rollbackCheckin(product);
     //      this.chekedinSubject.next(false);
     //    },
     //    reject: () => {
     //      //reject action
     //    }
     //  });

    } else {
      this.volonteerComponentService.checkin(product);
      this.chekedinSubject.next(true);
      //   await this.confirmationService.confirm({
      //     target: event.target,
      //     message: 'Är du säker på att du vill checka in ' + product.startNumber + ' ' +
      //       ' ' + product.givenName + ' ' + product.familyName,
      //     icon: 'pi pi-exclamation-triangle',
      //     accept: () => {
      //       this.volonteerComponentService.checkin(product);
      //       this.chekedinSubject.next(true);
      //     },
      //     reject: () => {
      //       //reject action
      //     }
      //   })
    }
  }

  async setdnf(product: any, event: Event) {
    if (!this.linkservice.exists(product.link, 'relation.volonteer.setdnf')) {
      this.volonteerComponentService.rollbackDnf(product);
      this.chekedinSubject.next(true);
      // await this.confirmationService.confirm({
      //   target: event.target,
      //   message: 'Är du säker på att du vill ångra DNF för ' + product.startNumber +
      //     '   ' + product.givenName + ' ' + product.familyName,
      //   icon: 'pi pi-exclamation-triangle',
      //   accept: () => {
      //     this.volonteerComponentService.rollbackDnf(product);
      //     this.chekedinSubject.next(true);
      //   },
      //   reject: () => {
      //     //reject action
      //   }
      // });

    } else {
      this.volonteerComponentService.setDnf(product);
      this.dnfSubject.next(false);
      // await this.confirmationService.confirm({
      //   target: event.target,
      //   message: 'Är du säker på att du vill markera DNF för ' + product.startNumber + ' ' +
      //     ' ' + product.givenName + ' ' + product.familyName,
      //   icon: 'pi pi-exclamation-triangle',
      //   accept: () => {
      //     this.volonteerComponentService.setDnf(product);
      //     this.dnfSubject.next(false);
      //   },
      //   reject: () => {
      //     //reject action
      //   }
      // })
    }
  }

}

export class VyInformation {
  statistics: Statistics;
  randonnerurs: ParticipantToPassCheckpointRepresentation[];
  choosenControl: string;
  choosen: unknown [] = [0];
  choosentrack: unknown [] = [0];
  hoosencheckpoint: unknown [] = [0];
}
