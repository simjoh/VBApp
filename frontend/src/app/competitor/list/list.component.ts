import {Component, ChangeDetectionStrategy, OnInit} from '@angular/core';
import {CompetitorListComponentService} from "./competitor-list-component.service";
import {RandonneurCheckPointRepresentation} from "../../shared/api/api";

@Component({
  selector: 'brevet-list',
  templateUrl: './list.component.html',
  styleUrls: ['./list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [CompetitorListComponentService]
})
export class ListComponent implements OnInit{


  checkpoints$ = this.comp.$controls;

  constructor(private comp: CompetitorListComponentService) { }


  checkin($event: any, s: RandonneurCheckPointRepresentation) {
    if ($event === true){
      this.comp.stamp($event,s)
    } else {
      this.comp.rollbackStamp($event,s)
    }

  }


  dnf($event: any, s: RandonneurCheckPointRepresentation) {
      this.comp.setDnf($event, s);
  }

  ngOnInit(): void {
    this.comp.reload();
  }
}
