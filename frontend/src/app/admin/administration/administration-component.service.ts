import {Injectable} from '@angular/core';
import {AdministrationService} from "./administration.service";

@Injectable()
export class AdministrationComponentService {

  constructor(private administrationservice: AdministrationService) {


    this.administrationservice.getFoundationForAcpReport();
  }
}
