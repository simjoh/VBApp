import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import {LogoComponentService} from "./logo-component.service";
import {map} from "rxjs/operators";
import {DatePipe} from "@angular/common";
import {environment} from "../../../environments/environment";

@Component({
    selector: 'brevet-logo',
    templateUrl: './logo.component.html',
    styleUrls: ['./logo.component.scss'],
    providers: [LogoComponentService],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class LogoComponent implements OnInit {

  @Input() rolling : boolean
  @Input() heigth: number
  @Input() width: number

  logoinfo = this.logocomponentService.$logo.pipe(
    map(all => {
      const date = new Date();
      const logotToShow = all.logos.find((logo:any) => {
        let logoreturn;
        if(date.toISOString().split('T')[0] === logo.startdate || date.toISOString().split('T')[0] <= logo.enddate && date.toISOString().split('T')[0] >= logo.startdate) {

          if (this.isProduction()){
            logoreturn = logo.logo.replace('{0}', 'prod')
          } else {
            logoreturn = logo.logo.replace('{0}', 'demo')
          }

        }
        if(!logo.startdate && !logo.endate){
          if (this.isProduction()){
           logoreturn = logo.logo.replace('{0}', 'prod')
          } else {
            logoreturn = logo.logo.replace('{0}', 'demo')
          }
        }
        logo.logo = logoreturn;
        return logo.logo;
      })
      return logotToShow;
    }),
    map(logo => {
      return {
        "rolling":  this.rolling,
        "heigth": this.heigth,
        "width": this.width,
        "logo": logo.logo
      } as Viewinformation
    })
  )

  private isProduction(): boolean{
    return environment.production
  }

  private replacePlaceholder(){


  }

  constructor(private logocomponentService:  LogoComponentService, public datepipe: DatePipe) { }

  ngOnInit(): void {
  }

}

export class Viewinformation {
  rolling: boolean;
  heigth: number;
  width: number;
  logo: string;
}
