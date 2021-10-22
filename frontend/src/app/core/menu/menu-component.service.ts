import { Injectable } from '@angular/core';
import {AuthService} from "../auth/auth.service";

@Injectable()
export class MenuComponentService {

  constructor(private authService: AuthService) { }


  $activeuser = this.authService.$auth$;

  logoutUser() {
    this.authService.logoutUser();
  }

  reload(){
    this.authService.reload();
  }
}
