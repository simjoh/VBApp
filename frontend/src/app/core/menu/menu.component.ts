import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {MenuComponentService} from "./menu-component.service";
import {map} from "rxjs/operators";
import {Observable} from "rxjs";
import { MenuItem } from 'primeng/api';
import { DeviceDetectorService } from 'ngx-device-detector';

@Component({
  selector: 'brevet-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss'],
  providers: [MenuComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MenuComponent implements OnInit{

  isMenuCollapsed = false

  $activeUser = this.menucomponentService.$activeuser.pipe(
    map(user =>{
      const vy = new VyInformation()
      for (var val of user.roles) {
        if (val === "COMPETITOR"){
            vy.competitor = true
            this.isMenuCollapsed = false;
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

        if (val === "USER"){
          vy.user = true;
        }
      }
      vy.namn = user.name;
      return vy
    })
  ) as Observable<VyInformation>

  constructor(private menucomponentService: MenuComponentService,  private deviceService: DeviceDetectorService) {
    if(this.deviceService.isDesktop()){
      this.isMenuCollapsed = false;
    }
    if (this.deviceService.isMobile()){
      this.isMenuCollapsed = true;
    }
  }

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
  user?: boolean;
}
