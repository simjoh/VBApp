import {Component, OnInit, ChangeDetectionStrategy} from '@angular/core';
import {MenuComponentService} from "./menu-component.service";
import {map} from "rxjs/operators";
import {BehaviorSubject, Observable} from "rxjs";
import {MenuItem} from 'primeng/api';
import {DeviceDetectorService} from 'ngx-device-detector';
import {Roles} from "../../shared/roles";

@Component({
  selector: 'brevet-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss'],
  providers: [MenuComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MenuComponent implements OnInit {
  deviceInfo = null;
  isMenuCollapsed = false


  $menuSubject = new BehaviorSubject<Array<MenuItem>>([]);
  $menuitems = this.$menuSubject.asObservable();

  items: MenuItem[] = [];

  $activeUser = this.menucomponentService.$activeuser.pipe(
    map(user => {


      if (user.roles.find(s => s.id === Roles.ADMIN) || user.roles.find(s => s.id === Roles.SUPERUSER)) {

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


        if (!this.items.some(item => item.label === 'Administration')) {
          this.items.push({
            label: 'Administration',
            routerLink: 'admin/administration/',
            expanded: true
          })
        }


        if (!this.items.some(item => item.label === 'Systeminställningar')) {
          let d = []
          d.push({
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
              label: 'Kontrollplatser',
              routerLink: '/admin/siteadmin/sites/'
            });


          if (user.roles.find(s => s.id === Roles.SUPERUSER)) {
            d.push({
              label: 'Arrangör',
              routerLink: '/admin/organizeradmin/organizers/'
            });
          }

          this.items.push({
              label: 'Systeminställningar',
              items: d
            }
          );
        }


      }

      if (user.roles.find(s => s.id === Roles.ACPREPRESENTIVE)) {
        this.items.push({
          label: 'Administration',
          routerLink: 'admin/administration/acp/brevet-acp-report/',
          expanded: true
        })
      }


      if (user.roles.length > 1 && user.roles.find(s => s.id === Roles.VOLONTAR) && !this.items.some(item => item.label === 'Volontär')) {
        this.items.push({
          label: 'Volontär',
          routerLink: '/volunteer',
        })
      }

      this.$menuSubject.next(this.items);

      return this.items;
      // return this.items.sort((a, b) => (a.label > b.label) ? 1 : -1)
    })
  ) as Observable<MenuItem[]>

  $logedinas = this.menucomponentService.$activeuser.pipe(
    map((val) => {
      return val.name;
    })
  )


  constructor(private menucomponentService: MenuComponentService, private deviceService: DeviceDetectorService) {
    if (this.deviceService.isDesktop()) {
      this.isMenuCollapsed = false;
    }
    if (this.deviceService.isMobile()) {
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


}

export class VyInformation {
  namn: string;
  admin?: boolean;
  volonteer?: boolean
  competitor?: boolean;
  superuser?: boolean;
  user?: boolean;
}
