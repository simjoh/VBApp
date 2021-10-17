import { Component, OnInit } from '@angular/core';

declare var ol: any;

@Component({
  selector: 'brevet-map',
  templateUrl: './map.component.html',
  styleUrls: ['./map.component.scss']
})

export class MapComponent implements OnInit{
  map: any;
  latitude: number = 18.5204;
  longitude: number = 73.8567;
  constructor() { 
    this.getLocation();
  }

   
  ngOnInit() { 
    this.getLocation()
    var mousePositionControl = new ol.control.MousePosition({
      coordinateFormat: ol.coordinate.createStringXY(4),
      projection: 'EPSG:4326',
      // comment the following two lines to have the mouse position
      // be placed within the map.
      className: 'custom-mouse-position',
      target: document.getElementById('mouse-position'),
      undefinedHTML: '&nbsp;'
    });
    this.map = new ol.Map({
      target: 'map',
      controls: ol.control.defaults({
        attributionOptions: {
          collapsible: false
        }
      }).extend([mousePositionControl]),
      layers: [
        new ol.layer.Tile({
          source: new ol.source.OSM()
        })
      ],
      view: new ol.View({
        center: ol.proj.fromLonLat([73.8567, 18.5204]),
        zoom: 8
      })
    });
  }

  setCenter() {
    var view = this.map.getView();
    view.setCenter(ol.proj.fromLonLat([this.longitude, this.latitude]));
    view.setZoom(8);
  }

  getLocation(): void{
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position)=>{
          this.longitude = position.coords.longitude;
          this.latitude = position.coords.latitude;
          this.setCenter();
          this.callApi(this.longitude,this.latitude);
        });
    } else {
      //TODO:ALERT USER THAT IT IS NOT POSSIBLE TO CHECKIN
       console.log("No support for geolocation")
    }
  }
  checkin(){
    console.log('Should submit')
    this.getLocation();
  }

  callApi(Longitude: number, Latitude: number){
    //const url = `https://api-adresse.data.gouv.fr/reverse/?lon=${Longitude}&lat=${Latitude}`
    //Call API
    console.log('Longitude',Longitude, 'Latitude',Latitude);
  }

}
