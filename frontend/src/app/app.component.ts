import {Component, Injector} from '@angular/core';
import {InititatedService} from "./core/inititated.service";
import {ServiceLocator} from "./core/locator.service";


@Component({
  selector: 'brevet-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  title = 'brevet-frontend';

  init$ = this.initiatedService.initierad$;


  constructor(injector: Injector, private initiatedService: InititatedService) {
    ServiceLocator.injector = injector;
  }
}
