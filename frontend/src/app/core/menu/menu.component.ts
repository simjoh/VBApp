import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {AuthService} from "../auth/auth.service";
import {MenuComponentService} from "./menu-component.service";
import {map} from "rxjs/operators";
import {Observable} from "rxjs";

@Component({
  selector: 'brevet-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss'],
  providers: [MenuComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MenuComponent implements OnInit {

  $activeUser = this.menucomponentService.$activeuser.pipe(
    map(user =>{
        return {
          namn: user.name,
          land:'SE'
        } as VyInformation
    })
  ) as Observable<VyInformation>

  constructor(private menucomponentService: MenuComponentService) { }

  ngOnInit(): void {
  }

  logout() {
    this.menucomponentService.logoutUser();
  }
}


export class VyInformation {
  namn: string;
  land: unknown;
}
