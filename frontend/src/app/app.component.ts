import {Component, Injector} from '@angular/core';
import {InititatedService} from "./core/inititated.service";
import {ServiceLocator} from "./core/locator.service";
import {AuthenticatedService} from "./core/auth/authenticated.service";


@Component({
  selector: 'brevet-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  title = 'Västerbottenbrevet';

  init$ = this.initiatedService.initierad$;
  $authenticated = this.authenticatedservice.authenticated$


  constructor(injector: Injector, private initiatedService: InititatedService, private authenticatedservice: AuthenticatedService) {
    ServiceLocator.injector = injector;
  }
}
