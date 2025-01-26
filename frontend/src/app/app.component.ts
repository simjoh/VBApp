import {Component, Injector, OnInit} from '@angular/core';
import {InititatedService} from "./core/inititated.service";
import {ServiceLocator} from "./core/locator.service";
import {AuthenticatedService} from "./core/auth/authenticated.service";
import {MenuItem, PrimeNGConfig} from "primeng/api";
import {SvgService} from "./shared/svg.service";
import {DomSanitizer} from "@angular/platform-browser";



@Component({
  selector: 'brevet-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit{
  title = 'Västerbottenbrevet';

  items: MenuItem[];

  init$ = this.initiatedService.initierad$;
  $authenticated = this.authenticatedservice.authenticated$


  constructor(private primengConfig: PrimeNGConfig,injector: Injector, private initiatedService: InititatedService, private authenticatedservice: AuthenticatedService, private svgcache: SvgService, private sanitizer: DomSanitizer) {
    ServiceLocator.injector = injector;
  }

  async ngOnInit() {
    this.primengConfig.ripple = true;
    await this.svgcache.preloadSvgs();


  }
}
