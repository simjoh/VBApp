import {Component, OnInit, ChangeDetectionStrategy, Input} from '@angular/core';
import {LogoComponentService} from "./logo-component.service";
import {map} from "rxjs/operators";
import {DatePipe} from "@angular/common";
import {newArray} from "@angular/compiler/src/util";

@Component({
  selector: 'brevet-logo',
  templateUrl: './logo.component.html',
  styleUrls: ['./logo.component.scss'],
  providers: [LogoComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class LogoComponent implements OnInit {

  @Input() rolling : boolean
  @Input() heigth: number
  @Input() width: number

  logoinfo = this.logocomponentService.$logo.pipe(
    map(all => {
      const date = new Date();
      const logotToShow = all.logos.find((logo:any) => {
        if(date.toISOString().split('T')[0] === logo.startdate || date.toISOString().split('T')[0] <= logo.enddate && date.toISOString().split('T')[0] >= logo.startdate) {
          return logo;
        }
        if(!logo.startdate && !logo.endate){
          return logo;
        }
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
