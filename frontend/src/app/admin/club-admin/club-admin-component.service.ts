import { Injectable } from '@angular/core';
import {ClubService} from "./club.service";

@Injectable()
export class ClubAdminComponentService {


  $allClubs = this.clubservice.$allclubs

  constructor(private clubservice: ClubService) { }
}
