import {Injectable} from '@angular/core';
import {AdministrationService} from "./administration.service";
import {AuthService} from "../../core/auth/auth.service";
import {LocalStorageService} from "../../core/local-storage/local-storage.service";

@Injectable()
export class AdministrationComponentService {



  $activeUser = this.authService.$auth$

  constructor(private administrationservice: AdministrationService, private authService: AuthService, private localstorage: LocalStorageService) {


    this.administrationservice.getFoundationForAcpReport();
  }
}
