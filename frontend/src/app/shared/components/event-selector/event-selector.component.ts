import { Component, OnInit, ChangeDetectionStrategy, Input, Output, EventEmitter } from '@angular/core';
import { EventSelectorComponentService } from "./event-selector-component.service";
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DropdownModule } from 'primeng/dropdown';
import { TooltipModule } from 'primeng/tooltip';

@Component({
  selector: 'brevet-event-selector',
  templateUrl: './event-selector.component.html',
  styleUrls: ['./event-selector.component.scss'],
  providers: [EventSelectorComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush,
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    DropdownModule,
    TooltipModule
  ]
})
export class EventSelectorComponent implements OnInit {
  @Input() filter: boolean;
  @Input() showclear: boolean;
  @Input() placeholder: string;
  @Input() styleClass: string;
  @Output() trackChange: EventEmitter<any> = new EventEmitter();

  selectedEvent: string;
  selected: [];
  $eventItems = this.eventselectorComponentService.$eventItems;

  constructor(private eventselectorComponentService: EventSelectorComponentService) { }

  ngOnInit(): void {
    this.eventselectorComponentService.trigger();
  }

  setValue($event: any) {
    this.trackChange.emit($event);
    this.eventselectorComponentService.currentEvent($event);
  }
}
