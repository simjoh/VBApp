import {Component, ChangeDetectionStrategy, OnInit, AfterViewInit} from '@angular/core';
import {CompetitorListComponentService} from "./competitor-list-component.service";
import {RandonneurCheckPointRepresentation} from "../../shared/api/api";
import {BehaviorSubject} from "rxjs";
import {map} from "rxjs/operators";

@Component({
  selector: 'brevet-list',
  templateUrl: './list.component.html',
  styleUrls: ['./list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [CompetitorListComponentService]
})
export class ListComponent implements OnInit, AfterViewInit{


  checkpoints$ = this.comp.$controls.pipe(
    map((controls) => {
      this.comp.dnfLinkExists(controls[0]).then((res) => {
        if (!res){
          this.dnfSubject.next(true);
        } else {
          this.dnfSubject.next(false);
        }
      })
      return controls;
    })

  );
  dnfknapptext: string

  constructor(private comp: CompetitorListComponentService) {
    this.dnfknapptext = "test";
  }


  async checkin($event: any, s: RandonneurCheckPointRepresentation,kontroller, index) {

    if ($event === true){
      await this.comp.stamp($event,s);
      localStorage.setItem('nextcheckpoint', JSON.stringify(kontroller.at(index + 1).checkpoint.checkpoint_uid));
     // this.scroll(kontroller.at(index +1).checkpoint.checkpoint_uid);
    } else {
      await this.comp.rollbackStamp($event,s);
      localStorage.setItem('nextcheckpoint', JSON.stringify(kontroller.at(index -1 ).checkpoint.checkpoint_uid));
    }
  }

  scroll(id) {
    // let el = document.getElementById(id);
    // if (el){
    //   el.scrollIntoView({behavior: 'smooth'});
    // }

  }

  dnfSubject = new BehaviorSubject(false);
  isdnf$ = this.dnfSubject.asObservable().pipe(
    map((val) => {
      if (val === true){
        this.dnfknapptext = 'Undo DNF'
      } else {
        this.dnfknapptext = 'DNF'
      }
      return val;
    })
  ).subscribe((dnf) => {
    console.log(dnf);
  });


  dnf($event: any, s: RandonneurCheckPointRepresentation) {
      this.comp.setDnf($event, s);
  }

  async dnf2(s: RandonneurCheckPointRepresentation) {
    await this.comp.dnfLinkExists(s).then(async (res) => {
      if (!res) {
        await this.comp.setDnf(false, s);
        this.dnfSubject.next(true);
      } else {
        await this.comp.setDnf(true, s);
        this.dnfSubject.next(false);
      }
    })

  }

  ngOnInit(): void {

    this.comp.reload();
  }

  ngAfterViewInit(): void {
    this.scroll(null)
  }
}
