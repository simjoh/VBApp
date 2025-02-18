import {Component, OnInit, ChangeDetectionStrategy, Output} from '@angular/core';
import {RusaPlannerControlInputRepresentation} from "../../../../shared/api/rusaTimeApi";
import {SiteRepresentation} from "../../../../shared/api/api";
import {TrackBuilderComponentService} from "../track-builder-component.service";

@Component({
    selector: 'brevet-track-builder-controls-form',
    templateUrl: './track-builder-controls-form.component.html',
    styleUrls: ['./track-builder-controls-form.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class TrackBuilderControlsFormComponent implements OnInit {



  rusatimeControls: Array<RusaPlannerControlInputRepresentation> = []

  constructor(private trackbuildercomponentService: TrackBuilderComponentService) { }

  ngOnInit(): void {
   // this.rusatimeControls.push(this.emptyControlObject());
  }

  addControl() {
   this.rusatimeControls.push(this.emptyControlObject());
  }


  private emptyControlObject(): RusaPlannerControlInputRepresentation{
   return {
     DISTANCE: null,
     SITE: "",
   } as RusaPlannerControlInputRepresentation;
  }

  addSite($event: any, i: number) {
    this.rusatimeControls[i].SITE = $event;
  }

  publish($event: any) {
    console.log($event);
    this.trackbuildercomponentService.addControls(this.rusatimeControls)
  }
}
