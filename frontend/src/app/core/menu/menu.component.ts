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

  items: MenuItem[] = [];

  $activeUser = this.menucomponentService.$activeuser.pipe(
    map(user =>{



      if (user.roles.includes("ADMIN") || user.roles.includes("SUPERUSER")) {

        if (!this.items.some(item => item.label === 'Start')) {
          this.items.push({
            label: 'Start',
            routerLink: '/admin/brevet-admin-start',
          })

        }


        if (!this.items.some(item => item.label === 'Deltagare')) {
          this.items.push({
            label: 'Deltagare',
            routerLink: '/admin/participant',
          })
        }

        if (!this.items.some(item => item.label === 'Banor')) {
          this.items.push({
            label: 'Banor',
            routerLink: '/admin/banor',
          })
        }
        if (!this.items.some(item => item.label === 'Systemadministration')) {
          this.items.push({
            label: 'Systemadministration',
            items: [{
              label: 'Användare',
              routerLink: '/admin/useradmin/user'
            },
              {
                label: 'Klubbar',
                routerLink: '/admin/clubadmin/'
              },
              {
                label: 'Siter',
                routerLink: '/admin/siteadmin/sites/'
              }
            ]
          });


        }

      }

      if (user.roles.length > 1 && user.roles.includes("VOLONTEER") && !this.items.some(item => item.label === 'Volontär')) {
        this.items.push({
          label: 'Volontär',
          routerLink: '/volunteer',
        })
      }

      return this.items;
     // return this.items.sort((a, b) => (a.label > b.label) ? 1 : -1)
    })
  ) as Observable<MenuItem[]>

  $logedinas = this.menucomponentService.$activeuser.pipe(
    map((val) => {
     return val.name;
    })
  )


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

    // this.items = [
    //   {
    //     label: 'Deltagare',
    //     routerLink: '/admin/participant',
    //   },
    //   {
    //     label: 'Banor',
    //     routerLink: '/admin/banor',
    //   },
    //   {
    //     label: 'Systemadministration',
    //     routerLink: '/admin/banor',
    //     items: [{
    //       label: 'Användare',
    //     },
    //     ]
    //   }
    // ];
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
