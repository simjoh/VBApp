import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
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
export class MenuComponent implements OnInit{

  $activeUser = this.menucomponentService.$activeuser.pipe(
    map(user =>{
      const vy = new VyInformation()
      for (var val of user.roles) {
        if (val === "COMPETITOR"){
            vy.competitor = true
        }
        if (val === "ADMIN"){
          vy.admin = true;
        }
        if (val === "SUPERUSER"){
          vy.superuser = true;
        }
        if (val === "VOLONTEER"){
          vy.volonteer = true;
        }
      }
      vy.namn = user.name;
      return vy
    })
  ) as Observable<VyInformation>

  constructor(private menucomponentService: MenuComponentService) { }

  logout() {
    this.menucomponentService.logoutUser();
  }

  ngOnInit(): void {
   this.menucomponentService.reload();
  }
}
export class VyInformation {
  namn: string;
  admin?: boolean;
  volonteer?: boolean
  competitor?: boolean;
  superuser?: boolean;
}
