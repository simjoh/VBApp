import {Injectable} from '@angular/core';
import {combineLatest, Observable, Subject, throwError} from "rxjs";
import {catchError, map, shareReplay, startWith, tap} from "rxjs/operators";
import {EventRepresentation} from "../../shared/api/api";
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";

@Injectable({
	providedIn: 'root'
})
export class EventService {

	removeSubject = new Subject<string>()
	relaod$ = this.removeSubject.asObservable().pipe(
		startWith(''),
	);

	eventReloadAction = new Subject<EventRepresentation>()
	$eventReload = this.eventReloadAction.asObservable().pipe(
		startWith(null),
	);

	allEvents$ = this.getAllEvents() as Observable<EventRepresentation[]>;

	private userInsertedSubject = new Subject<EventRepresentation>();
	userInsertedAction$ = this.userInsertedSubject.asObservable().pipe(
		startWith(''),
	);


	eventsWithAdd$ = combineLatest([this.getAllEvents(), this.userInsertedAction$, this.relaod$, this.$eventReload]).pipe(
		map(([all, insert, del, eventreload]) => {
			if (insert) {
				return [...all, insert]
			}
			if (del) {
				var index = all.findIndex((elt) => elt.event_uid === del);
				all.splice(index, 1);
				const userArray = all;
				return this.deepCopyProperties(all);
			}

			if (eventreload) {
				var indexreload = all.findIndex((elt) => elt.event_uid === eventreload.event_uid);
				all[indexreload] = eventreload;

			}
			return this.deepCopyProperties(all);
		}),
	);

	constructor(private httpClient: HttpClient) {
	}


	async newEvent(newSite: EventRepresentation) {
		const user = await this.addSite(newSite);
		this.userInsertedSubject.next(user);
	}

	public getAllEvents(): Observable<EventRepresentation[]> {
		return this.httpClient.get<EventRepresentation[]>(environment.backend_url + "events").pipe(
			map((events: Array<EventRepresentation>) => {
				return events;
			}),
			tap(events => console.log("All events", events)),
			shareReplay(1)
		);
	}

	public getEvent(eventUid: string): Observable<EventRepresentation> {
		return this.httpClient.get<EventRepresentation>(environment.backend_url + "event/" + eventUid).pipe(
			map((event: EventRepresentation) => {
				return event;
			}),
			tap(event => console.log(event))
		) as Observable<EventRepresentation>
	}

	async addSite(event: EventRepresentation) {
		return await this.httpClient.post<EventRepresentation>(environment.backend_url + "event/", event).pipe(
			map((site: EventRepresentation) => {
				return event;
			}),
			tap(event => console.log(event))
		).toPromise();
	}

	public deleterEvent(eventUid: string) {
		return this.httpClient.delete(environment.backend_url + "event/" + eventUid)
			.pipe(
				catchError(err => {
					return throwError(err);
				})
			).toPromise().then((s) => {
				this.removeSubject.next(eventUid);
			})
	}

	public updateEvent(eventuid: string, event: EventRepresentation) {
		console.log('EventService.updateEvent called with:', {
			eventuid: eventuid,
			event: event
		});

		return this.httpClient.put<EventRepresentation>(environment.backend_url + "event/" + eventuid, event as EventRepresentation).pipe(
			map((event: EventRepresentation) => {
				console.log('EventService.updateEvent response:', event);
				this.eventReloadAction.next(event)
				return event;
			}),
			tap(event => console.log(event))
		).toPromise()
	}

	deepCopyProperties(obj: any): any {
		// Konverterar till och från JSON, kopierar properties men tappar bort metoder
		return obj === null || obj === undefined ? obj : JSON.parse(JSON.stringify(obj));
	}
}
