import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {MenuComponentService} from "./menu-component.service";
import {map} from "rxjs/operators";
import {BehaviorSubject, Observable} from "rxjs";
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
  deviceInfo = null;
  isMenuCollapsed = false


  $menuSubject = new BehaviorSubject<Array<MenuItem>>([]);
  $menuitems = this.$menuSubject.asObservable();

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
            expanded: true
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
                label: 'Events',
                routerLink: '/admin/eventadmin/events'
              },
              {
                label: 'Klubbar',
                routerLink: '/admin/clubadmin/'
              },
              {
                label: 'Organisatörer',
                routerLink: '/admin/organizeradmin/'
              },
              {
                label: 'Kontrollplatser',
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

      // if (!this.items.some(item => item.label === 'Logout')) {
      //   if (!this.deviceService.isDesktop()){
      //     if (this.deviceService.isMobile()  ||  this.deviceService.isTablet()){
      //       this.items.push({
      //         label: 'Logout',
      //         icon: 'pi pi-fw pi-sign-out',
      //         styleClass: "lg:hidden",
      //         command:()=> this.logout(),
      //       })
      //     }
      //   }
      //
      // }

      this.$menuSubject.next(this.items);

      return this.items;
     // return this.items.sort((a, b) => (a.label > b.label) ? 1 : -1)
    })
  ) as Observable<MenuItem[]>

  $logedinas = this.menucomponentService.$activeuser.pipe(
    map((val) => {

      if (val.roles.includes('COMPETITOR')) {
        return val.startnumber;
      }
      return null;
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
   this.menucomponentService.reload();
  }

  test() {
  }

  onResize($event: any) {
    // const userAgent = window.navigator.userAgent;
    // if (this.deviceService.isMobile(window.navigator.userAgent)) {
    //
    //   let items = this.$menuSubject.value
    //   if (!items.some(item => item.label === 'Logout')) {
    //
    //     items.push({
    //       label: 'Logout',
    //       icon: 'pi pi-fw pi-sign-out',
    //       styleClass: "lg:hidden",
    //       command:()=> this.logout(),
    //     })
    //
    //     this.$menuSubject.next(items)
    //   } else {
    //     console.log(items.length)
    //     if (items.some(item => item.label === 'Logout')) {
    //       console.log(this.deviceService.isMobile(window.navigator.userAgent) + 'Ska filtrerar')
    //         items = items.filter(function (item) {
    //           return item.label != 'Logout';
    //         })
    //     }
    //     console.log(items)
    //     this.$menuSubject.next(items)
    //   }
    //
    // }
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
