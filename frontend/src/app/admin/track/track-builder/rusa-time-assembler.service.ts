import { Injectable } from '@angular/core';
import {CONTROL_ITEM, CONTROLS, EVENT, RusaTimeRepresentation} from "../../../shared/api/rusaTimeApi";
import {CONTROL} from "@angular/cdk/keycodes";
import {SiteSelectorComponentService} from "../../../shared/components/site-selector/site-selector-component.service";

@Injectable()
export class RusaTimeAssemblerService {

  constructor() {
  }




  rusaTimeFormatFrom(): RusaTimeRepresentation{

    let controls = new Array<CONTROL_ITEM>()


    const event = this.eventFrom()


    controls.push(this.controlFrom())


    let control = {
      items: controls
    } as CONTROLS


    return {
      meta: null,
      controls: control,
      event: this.eventFrom()
    } as RusaTimeRepresentation;

  }


  eventFrom(): EVENT {

    return {

    } as EVENT
  }

  controlFrom(): CONTROL_ITEM {

    return   {
      CONTROL_NUMBER: 2
    } as CONTROL_ITEM;
  }
}
