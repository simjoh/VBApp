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
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
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
            route: '/admin/brevet-admin-start',
          })

        }

        if (!this.items.some(item => item.label === 'Deltagare')) {
          this.items.push({
            label: 'Deltagare',
            route: '/admin/participant',
          })
        }

        if (!this.items.some(item => item.label === 'Banor')) {
          this.items.push({
            label: 'Banor',
            route: '/admin/banor',
            expanded: true
          })
        }


        if (!this.items.some(item => item.label === 'Administration')) {
          this.items.push({
            label: 'Administration',
            route: 'admin/administration/',
            expanded: true
          })
        }


        if (!this.items.some(item => item.label === 'Systeminställningar')) {
          let d = []
          d.push({
              label: 'Användare',
              route: '/admin/useradmin/user'
            },
            {
              label: 'Events',
              route: '/admin/eventadmin/events'
            },
            {
              label: 'Klubbar',
              route: '/admin/clubadmin/'
            },
            {
              label: 'Kontrollplatser',
              route: '/admin/siteadmin/sites/'
            });


          if (user.roles.find(s => s.id === Roles.SUPERUSER)) {
            d.push({
              label: 'Arrangör',
              route: '/admin/organizeradmin/organizers/'
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
          route: 'admin/administration/acp/brevet-acp-report/',
          expanded: true
        })
      }


      // if (user.roles.length > 1 && user.roles.find(s => s.id === Roles.VOLONTAR) && !this.items.some(item => item.label === 'Volontär')) {
      //   this.items.push({
      //     label: 'Volontär',
      //     routerLink: '/volunteer',
      //   })
      // }

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
