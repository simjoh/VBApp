import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {ClubAdminComponentService} from "../club-admin-component.service";
import {BehaviorSubject} from "rxjs";
import {ClubRepresentation} from "../../../shared/api/api";
import {map} from "rxjs/operators";

@Component({
  selector: 'brevet-club-list',
  templateUrl: './club-list.component.html',
  styleUrls: ['./club-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ClubListComponent implements OnInit {


  $serachDisabledSubject = new BehaviorSubject(true)


  $clubs = this.clubadminComponentService.$allClubs;


  $searchDisabled = this.$clubs.pipe(
    map((vals: Array<ClubRepresentation>) => {
      return vals.length === 0;
    })
  );

  constructor(private clubadminComponentService: ClubAdminComponentService) { }

  ngOnInit(): void {
  }

}
