import {Component, OnInit, ChangeDetectionStrategy, ViewChild, AfterViewInit, Input} from '@angular/core';
import {OverlayPanel} from "primeng/overlaypanel";

@Component({
    selector: 'brevet-overlay',
    templateUrl: './overlay.component.html',
    styleUrls: ['./overlay.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    standalone: false
})
export class OverlayComponent implements OnInit  {


  @ViewChild("op") overlayPanel: any


  @Input() icon: string
  @Input() heigth: string
  @Input() width: string
  @Input() showCloseIcon: boolean
  @Input() mouseActivate: boolean


  constructor() { }

  ngOnInit(): void {

  }



}
