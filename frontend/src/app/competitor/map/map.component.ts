import { Component, OnInit } from '@angular/core';

declare var ol: any;

@Component({
  selector: 'brevet-map',
  templateUrl: './map.component.html',
  styleUrls: ['./map.component.scss']
})
//var checkpoints = [{name:'test',lan:20.3,lat:63},{name:'test 2',lan:20.3, lat:63}]
export class MapComponent implements OnInit{
  map: any;
  checkpoints: any;
  latitude: number = 18.5204;
  longitude: number = 73.8567;
  constructor() {
    this.getLocation();
  }


  ngOnInit() {
    var checkpoints = [{name:'test',lan:20.3,lat:63},{name:'test 2',lan:20.3, lat:63}]
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
    //this.addBiker(this.latitude, this.longitude);
  }

  setCenter() {
    var view = this.map.getView();
    view.setCenter(ol.proj.fromLonLat([this.longitude, this.latitude]));
    view.setZoom(8);
    this.addBiker(this.latitude, this.longitude)
    this.addCheckpoint(this.latitude, this.longitude,'Kontroll 1')
  }

  getLocation(): void{
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position)=>{
          this.longitude = position.coords.longitude;
          this.latitude = position.coords.latitude;
          this.setCenter();
        });
    } else {
      //TODO:ALERT USER THAT IT IS NOT POSSIBLE TO CHECKIN
    }
  }
  checkin(){
    this.getLocation();
    this.callApi(this.longitude,this.latitude);
  }

  callApi(Longitude: number, Latitude: number){
    //const url = `https://api-adresse.data.gouv.fr/reverse/?lon=${Longitude}&lat=${Latitude}`
    //Call API
    alert('Registrerad kör vidare till nästa kontreoll')
  }
  addCheckpoint(lat: number, lng: number,checkpoint_name:string) {
    var vectorLayer = new ol.layer.Vector({
      source: new ol.source.Vector({
        features: [new ol.Feature({
          geometry: new ol.geom.Point(ol.proj.transform([lng+0.1, lat+0.1], 'EPSG:4326', 'EPSG:3857')),
        })]
      }),

      style: new ol.style.Style({
        text: new ol.style.Text({
          text: checkpoint_name,
          scale: 1.2,
          //fill: new ol.style.Fill({
          //  color: "#fff"
          //}),
          //stroke: new ol.style.Stroke({
          //  color: "0",
          //  width: 3
          //})
        })
      //  /image: new ol.style.Icon({
          //anchor: [0.5, 0.5],
          //anchorXUnits: "fraction",
          //anchorYUnits: "fraction",
          //scale: 0.04,
          //src: 'https://cdn-icons-png.flaticon.com/512/565/565350.png' //"assets/img/my-icon.png"
      //  })
       })
    });
    this.map.addLayer(vectorLayer);
    }

  addBiker(lat: number, lng: number) {
    var vectorLayer = new ol.layer.Vector({
      source: new ol.source.Vector({
        features: [new ol.Feature({
          geometry: new ol.geom.Point(ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857')),
        })]
      }),
      style: new ol.style.Style({
        image: new ol.style.Icon({
          anchor: [0.5, 0.5],
          anchorXUnits: "fraction",
          anchorYUnits: "fraction",
          scale: 0.04,
          src: 'https://cdn-icons-png.flaticon.com/512/565/565350.png' //"assets/img/my-icon.png"
        })
       })
    });
    this.map.addLayer(vectorLayer);
    }
}
