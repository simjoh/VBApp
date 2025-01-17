import { ChangeDetectionStrategy, Component } from '@angular/core';
import {AdministrationComponentService} from "./administration-component.service";

@Component({
  selector: 'brevet-administration',
  templateUrl: './administration.component.html',
  styleUrls: ['./administration.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [AdministrationComponentService]
})
export class AdministrationComponent {

  constructor(administrationcomponentservice: AdministrationComponentService) {


  }

}
