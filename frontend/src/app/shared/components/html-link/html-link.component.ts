import {Component, OnInit, ChangeDetectionStrategy, Input, OnChanges, SimpleChanges} from '@angular/core';
import {BehaviorSubject, ReplaySubject} from "rxjs";
import {map} from "rxjs/operators";
import {environment} from "../../../../environments/environment";
import {Link} from "../../api/api";
import {LinkService} from "../../../core/link.service";
import {HttpMethod} from "../../../core/HttpMethod";


@Component({
  selector: 'brevet-html-link',
  templateUrl: './html-link.component.html',
  styleUrls: ['./html-link.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class HtmlLinkComponent implements OnInit, OnChanges {

  vyinformationSubject = new BehaviorSubject(new Vyinformation());
  $vyinformation = this.vyinformationSubject.pipe(
    map((val) => {
      const link = this.linkService.findByRel(this.link,this.rel, HttpMethod.GET as string)
      if (!val.text){
        val.text = val.link + link.url;
      }
      val.link = val.link + link.url;
      return val;
    })
  )

  @Input() target: string
  @Input() text: string
  @Input() link: Link[]
  @Input() rel: string;

  constructor(private linkService: LinkService) { }

  ngOnInit(): void {
    const vyinfo = new Vyinformation();
    vyinfo.target = this.target;
    vyinfo.text = this.text;
    vyinfo.link = environment.http_url;
    this.vyinformationSubject.next(vyinfo)
  }

  ngOnChanges(changes: SimpleChanges): void {
    // if (changes &&  ) {
    //   const vyinfo = new Vyinformation();
    //   vyinfo.target = changes.target.currentValue;
    //   vyinfo.text = changes.text.currentValue;
    //   vyinfo.link = environment.http_url + "/"
    //   this.vyinformationSubject.next(vyinfo)
    // }
  }

}


export class Vyinformation {
  target: string;
  text: string;
  link: string;
}
