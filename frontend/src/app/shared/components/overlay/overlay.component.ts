import {Component, OnInit, ChangeDetectionStrategy, ViewChild, AfterViewInit, Input} from '@angular/core';
import {OverlayPanel} from "primeng/overlaypanel";

@Component({
  selector: 'brevet-overlay',
  templateUrl: './overlay.component.html',
  styleUrls: ['./overlay.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class OverlayComponent implements OnInit  {


  @ViewChild("op") overlayPanel: any


  @Input() icon: string
  @Input() heigth: string
  @Input() showCloseIcon: boolean


  constructor() { }

  ngOnInit(): void {

  }



}
