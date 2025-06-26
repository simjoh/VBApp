import {Component, OnInit, Input, Output, EventEmitter} from '@angular/core';
import {Site, SiteRepresentation} from '../../api/api';
import {SiteSelectorComponentService} from "./site-selector-component.service";

@Component({
  selector: 'brevet-site-selector',
  templateUrl: './site-selector.component.html',
  styleUrls: ['./site-selector.component.scss'],
  providers: [SiteSelectorComponentService]
})
export class SiteSelectorComponent implements OnInit {

  @Input() imageSize: Number;
  @Input() filter: boolean;
  @Input() showclear: boolean;
  @Input() placeholder: string;
  @Input() styleClass: string;
  @Output() SiteChange: EventEmitter<any> = new EventEmitter();
  @Output() SiteRepresentattionChange: EventEmitter<any> = new EventEmitter();

  selectedEvent: Site;
  sites: Site[] = [];

  $sites = this.siteselectorComponentService.$allSites;

  constructor(private siteselectorComponentService: SiteSelectorComponentService) {
    console.log('SiteSelectorComponent: Constructor called');
  }

  ngOnInit(): void {
    console.log('SiteSelectorComponent: ngOnInit called');
    console.log('SiteSelectorComponent: Initial sites array length:', this.sites.length);

    // Subscribe to sites and populate the array
    this.$sites.subscribe(
      sites => {
        console.log('SiteSelectorComponent: Sites received in subscription:', sites);
        console.log('SiteSelectorComponent: Sites count:', sites ? sites.length : 'null/undefined');
        console.log('SiteSelectorComponent: Sites type:', typeof sites);
        console.log('SiteSelectorComponent: Sites is array:', Array.isArray(sites));

        if (sites && Array.isArray(sites)) {
          this.sites = sites;
          console.log('SiteSelectorComponent: Sites array updated to length:', this.sites.length);
          console.log('SiteSelectorComponent: First site example:', this.sites[0]);
        } else {
          console.warn('SiteSelectorComponent: Sites is not a valid array:', sites);
          this.sites = [];
        }
      },
      error => {
        console.error('SiteSelectorComponent: Error in sites subscription:', error);
        this.sites = [];
      },
      () => {
        console.log('SiteSelectorComponent: Sites observable completed');
      }
    );

    // Let's also try a direct call to the service to see if it works
    setTimeout(() => {
      console.log('SiteSelectorComponent: After timeout - sites array length:', this.sites.length);
    }, 2000);
  }

  setValue($event: any) {
    console.log('SiteSelectorComponent: setValue called with:', $event);
    this.SiteChange.emit(this.selectedEvent.site_uid)
    this.siteselectorComponentService.currentEvent(this.selectedEvent)
    this.SiteRepresentattionChange.emit(this.selectedEvent)
  }
}
