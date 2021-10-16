import {Injectable, PipeTransform} from '@angular/core';

import {BehaviorSubject, Observable, of, Subject} from 'rxjs';

import {Competitor} from './competitor';
import {COUNTRIES} from './competitors';
import {DecimalPipe} from '@angular/common';
import {debounceTime, delay, switchMap, tap} from 'rxjs/operators';
import {SortColumn, SortDirection} from './sortable.directive';

interface SearchResult {
  competitors: Competitor[];
  total: number;
}

interface State {
  page: number;
  pageSize: number;
  searchTerm: string;
  sortColumn: SortColumn;
  sortDirection: SortDirection;
}

const compare = (v1: string | number, v2: string | number) => v1 < v2 ? -1 : v1 > v2 ? 1 : 0;

function sort(competitors: Competitor[], column: SortColumn, direction: string): Competitor[] {
  if (direction === '' || column === '') {
    return competitors;
  } else {
    return [...competitors].sort((a, b) => {
      const res = compare(a[column], b[column]);
      return direction === 'asc' ? res : -res;
    });
  }
}

function matches(competitor: Competitor, term: string, pipe: PipeTransform) {
  return competitor.name.toLowerCase().includes(term.toLowerCase())
    || pipe.transform(competitor.area).includes(term)
    || pipe.transform(competitor.population).includes(term);
}

@Injectable({providedIn: 'root'})
export class CompetitorService {
  private _loading$ = new BehaviorSubject<boolean>(true);
  private _search$ = new Subject<void>();
  private _competitors$ = new BehaviorSubject<Competitor[]>([]);
  private _total$ = new BehaviorSubject<number>(0);

  private _state: State = {
    page: 1,
    pageSize: 5,
    searchTerm: '',
    sortColumn: '',
    sortDirection: ''
  };

  constructor(private pipe: DecimalPipe) {
    this._search$.pipe(
      tap(() => this._loading$.next(true)),
      debounceTime(200),
      switchMap(() => this._search()),
      delay(200),
      tap(() => this._loading$.next(false))
    ).subscribe(result => {
      this._competitors$.next(result.competitors);
      this._total$.next(result.total);
    });

    this._search$.next();
  }

  get competitors$() { return this._competitors$.asObservable(); }
  get total$() { return this._total$.asObservable(); }
  get loading$() { return this._loading$.asObservable(); }
  get page() { return this._state.page; }
  get pageSize() { return this._state.pageSize; }
  get searchTerm() { return this._state.searchTerm; }

  set page(page: number) { this._set({page}); }
  set pageSize(pageSize: number) { this._set({pageSize}); }
  set searchTerm(searchTerm: string) { this._set({searchTerm}); }
  set sortColumn(sortColumn: SortColumn) { this._set({sortColumn}); }
  set sortDirection(sortDirection: SortDirection) { this._set({sortDirection}); }

  private _set(patch: Partial<State>) {
    Object.assign(this._state, patch);
    this._search$.next();
  }

  private _search(): Observable<SearchResult> {
    const {sortColumn, sortDirection, pageSize, page, searchTerm} = this._state;

    // 1. sort
    let competitors = sort(COUNTRIES, sortColumn, sortDirection);

    // 2. filter
    competitors = competitors.filter(competitor => matches(competitor, searchTerm, this.pipe));
    const total = competitors.length;

    // 3. paginate
    competitors = competitors.slice((page - 1) * pageSize, (page - 1) * pageSize + pageSize);
    return of({competitors, total});
  }
}