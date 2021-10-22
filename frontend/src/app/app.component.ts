import {Component, Injector, OnInit} from '@angular/core';
import {InititatedService} from "./core/inititated.service";
import {ServiceLocator} from "./core/locator.service";
import {AuthenticatedService} from "./core/auth/authenticated.service";
import {PrimeNGConfig} from "primeng/api";


@Component({
  selector: 'brevet-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit{
  title = 'Västerbottenbrevet';

  init$ = this.initiatedService.initierad$;
  $authenticated = this.authenticatedservice.authenticated$


  constructor(private primengConfig: PrimeNGConfig,injector: Injector, private initiatedService: InititatedService, private authenticatedservice: AuthenticatedService) {
    ServiceLocator.injector = injector;
  }

  ngOnInit() {
    this.primengConfig.ripple = true;
  }
}
