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
  }

  ngOnInit(): void {
    // Subscribe to sites and populate the array
    this.$sites.subscribe(
      sites => {
        if (sites && Array.isArray(sites)) {
          this.sites = sites;
        } else {
          console.warn('SiteSelectorComponent: Sites is not a valid array:', sites);
          this.sites = [];
        }
      },
      error => {
        console.error('SiteSelectorComponent: Error in sites subscription:', error);
        this.sites = [];
      }
    );
  }

  setValue($event: any) {
    this.SiteChange.emit(this.selectedEvent.site_uid)
    this.siteselectorComponentService.currentEvent(this.selectedEvent)
    this.SiteRepresentattionChange.emit(this.selectedEvent)
  }
}
