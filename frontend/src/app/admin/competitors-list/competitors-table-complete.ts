import {DecimalPipe} from '@angular/common';
import {Component, QueryList, ViewChildren} from '@angular/core';
import {Observable} from 'rxjs';

import {Competitor} from './competitor';
import {CompetitorService} from './competitor.service';
import {NgbdSortableHeader, SortEvent} from './sortable.directive';


@Component(
    {selector: 'ngbd-table-complete',
    templateUrl: './table-complete.html',
    providers: [CompetitorService, DecimalPipe]})
export class NgbdTableComplete {
  competitors$: Observable<Competitor[]>;
  total$: Observable<number>;

  @ViewChildren(NgbdSortableHeader) headers: QueryList<NgbdSortableHeader>;

  constructor(public service: CompetitorService) {
    this.competitors$ = service.competitors$;
    this.total$ = service.total$;
  }

  onSort({column, direction}: SortEvent) {
    // resetting other headers
    this.headers.forEach(header => {
      if (header.sortable !== column) {
        header.direction = '';
      }
    });

    this.service.sortColumn = column;
    this.service.sortDirection = direction;
  }

  test() {
    console.log("ssssssssssssssssssss")
  }
}
