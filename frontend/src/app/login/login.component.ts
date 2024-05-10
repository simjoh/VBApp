import {Component, OnInit, ChangeDetectionStrategy, ViewChild, AfterViewInit} from '@angular/core';
import {AuthService} from "../core/auth/auth.service";
import {LoginComponentService} from "./login-component.service";
import {NgForm} from "@angular/forms";
import {EventsService} from "../core/events/events.service";
import {EventType} from "../core/events/aevents";
import {map, tap} from "rxjs/operators";
import {Observable} from "rxjs";
import {environment} from "../../environments/environment";

@Component({
  selector: 'brevet-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
  providers: [LoginComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class LoginComponent implements  OnInit{

  @ViewChild("form") form: NgForm
  loginModel$ = this.loginComponetService.loginModel$

  prod = environment.production

  constructor(private authServiceService: AuthService, private loginComponetService: LoginComponentService, private eventService: EventsService) {
  }

  loginerror$ = this.eventService.händelser([EventType.Error], 200).pipe(
    map((event) => {
      return {
        meddelande: event.data,
      } as ViewInformation;
    }),
    tap((vyinfo) => {
      console.log("ErrorInfo", vyinfo);
    })
  ) as Observable<ViewInformation>;

  login(){
    // @ts-ignore
    if (!this.form.invalid){
      this.authServiceService.loginUser(this.loginComponetService.loginModel$).then((ss) => {
      });
    }


  }


  ngOnInit(): void {
    this.loginComponetService.initiateModel();
  }


}

export class ViewInformation {
  meddelande: string;
}



