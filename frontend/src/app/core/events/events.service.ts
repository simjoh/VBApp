import {Injectable} from '@angular/core';
import {Observable, ReplaySubject} from "rxjs";
import {delay, filter} from "rxjs/operators";
import {AEvent, EventType} from "./aevents";


@Injectable({
  providedIn: 'root'
})
export class EventsService {

  private eventSubject = new ReplaySubject<AEvent>(1);

  constructor() {
  }

  nyHändelse(typ: EventType, data: unknown) {
    this.eventSubject.next(new AEvent(typ, data));
  }

  händelser(typer?: (EventType | EventType[])[], delayMillis: number = 0): Observable<AEvent> {
    return this.eventSubject.asObservable()
      .pipe(
        filter(händelse => !typer || typer.filter((typ: EventType | EventType[]) => {
          const typlista = Array.isArray(typ) ? typ : [typ];
          return typlista.includes(händelse.type);
        }).length > 0),
        delay(delayMillis)
      );
  }
}
