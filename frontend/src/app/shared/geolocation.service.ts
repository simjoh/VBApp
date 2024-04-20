import {Injectable} from '@angular/core';
import {Observable, Subject} from 'rxjs';
import {Position} from "./position";

@Injectable({
	providedIn: 'root',
})
export class GeolocationService {

	private watchId: number;
	private positionSubject: Subject<Position> = new Subject<Position>();

	getCurrentPosition(): Observable<Position> {
		return new Observable((observer) => {
			if ('geolocation' in navigator) {
				var options = {maximumAge: 10000};
				navigator.geolocation.getCurrentPosition(
					(position) => {
						observer.next(position);
						observer.complete();
					},
					(error) => {
						observer.error(error);
					}
					, options);
			} else {
				observer.error('Geolocation is not available in this browser.');
			}
		});
	}

	getCurrentPositionWithOptions(options?: PositionOptions): Observable<Position> {
		return new Observable((observer) => {
			if ('geolocation' in navigator) {
				navigator.geolocation.getCurrentPosition(
					(position: Position) => {
						observer.next(position);
						observer.complete();
					},
					(error: any) => {
						observer.error(error);
					},
					options
				);
			} else {
				observer.error('Geolocation is not available in this browser.');
			}
		});
	}

	watchPosition(options?: PositionOptions): Observable<Position> {
		return new Observable((observer) => {
			if ('geolocation' in navigator) {
				this.watchId = navigator.geolocation.watchPosition(
					(position: Position) => {
						observer.next(position);
					},
					(error: any) => {
						observer.error(error);
					},
					options
				);
			} else {
				observer.error('Geolocation is not available in this browser.');
			}
			// Cleanup on unsubscribe
			return {
				unsubscribe: () => {
					if (this.watchId) {
						navigator.geolocation.clearWatch(this.watchId);
					}
				}
			};
		});
	}


	stopWatch(){
		setTimeout(() => {
			navigator.geolocation.clearWatch(this.watchId);
			console.log('Stopped watching position after 60 seconds.');
		}, 60000);
	}


}
