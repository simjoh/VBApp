import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {GeolocationService} from "../shared/geolocation.service";

@Component({
    selector: 'brevet-competitor',
    templateUrl: './competitor.component.html',
    styleUrls: ['./competitor.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class CompetitorComponent implements OnInit {

  constructor(private geolocationService: GeolocationService) { }

  ngOnInit(): void {
    this.getGeoLocation();
  }

  getGeoLocation() {
    this.geolocationService.getCurrentPosition().subscribe({
      next: (position) => {
        console.log('Latitude:', position.coords.latitude);
        console.log('Longitude:', position.coords.longitude);
      },
      error: (error) => {
        console.error('Error getting geolocation:', error);
      },
    });
  }

}
