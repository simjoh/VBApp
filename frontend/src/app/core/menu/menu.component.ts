import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import {AuthService} from "../auth/auth.service";

@Component({
  selector: 'brevet-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MenuComponent implements OnInit {

  constructor(private authService: AuthService) { }

  ngOnInit(): void {
  }

  logout() {
    this.authService.logoutUser();
  }
}
