import {AfterViewInit, ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {CompetitorListComponentService} from "./competitor-list-component.service";
import {RandonneurCheckPointRepresentation} from "../../shared/api/api";
import {BehaviorSubject, firstValueFrom} from "rxjs";
import {map} from "rxjs/operators";
import {GeolocationService} from "../../shared/geolocation.service";

@Component({
	selector: 'brevet-list',
	templateUrl: './list.component.html',
	styleUrls: ['./list.component.scss'],
	changeDetection: ChangeDetectionStrategy.OnPush,
	providers: [CompetitorListComponentService]
})
export class ListComponent implements OnInit, AfterViewInit {

	lat;
	long;
	within = true;

	checkpoints$ = this.comp.$controls.pipe(
		map((controls) => {
			this.comp.dnfLinkExists(controls[0]).then((res) => {
				if (!res) {
					this.dnfSubject.next(true);
				} else {
					this.dnfSubject.next(false);
				}
			})
			return controls;
		})
	);
	dnfknapptext: string

	constructor(private comp: CompetitorListComponentService, private geolocationService: GeolocationService) {
		this.dnfknapptext = "test";
	}

	async getGeoLocation() {
		await firstValueFrom(this.geolocationService.getCurrentPosition()).then(position => {
			const currentTimestamp = Date.now();
			const timeDifference = Math.abs(currentTimestamp - position.timestamp);
			const timeDifferenceInMinutes = timeDifference / (1000 * 60);
			if (timeDifferenceInMinutes <= 1) {
				this.within = true;
				console.log("Current timestamp is within one minute from the given timestamp.");
			} else {
				this.within = false;
				console.log("Current timestamp is more than one minute away from the given timestamp.");
			}
			console.log('Latitude:', position.coords.latitude);
			console.log('Longitude:', position.coords.longitude);
			this.lat = position.coords.latitude
			this.long = position.coords.longitude;
		});
	}

	sleep(ms) {
		return new Promise(resolve => setTimeout(resolve, ms));
	}


	async checkin($event: any, s: RandonneurCheckPointRepresentation, kontroller, index) {
		await this.getGeoLocation();
		if (!this.within) {
			while (!this.within) {
				await this.sleep(1000).then(() => {
					this.getGeoLocation();
				});
			}
		}
		if ($event === true) {
			await this.comp.stamp($event, s, this.lat, this.long);
			localStorage.setItem('nextcheckpoint', JSON.stringify(kontroller.at(index + 1).checkpoint.checkpoint_uid));
			let nextindex = this.nextIndexForward(index, kontroller)
			setTimeout(() => {
				console.log(kontroller.at(nextindex.checkpoint.checkpoint_uid));
				this.scroll(nextindex.checkpoint.checkpoint_uid);
			}, 2000);
		} else {
			await this.comp.rollbackStamp($event, s);
			let nextindex = this.nextIndexBackward(index, kontroller)
			setTimeout(() => {
				this.scroll(nextindex.checkpoint.checkpoint_uid);
			}, 2000);

			localStorage.setItem('nextcheckpoint', JSON.stringify(kontroller.at(nextindex).checkpoint.checkpoint_uid));
		}
	}


	private nextIndexBackward(index, kontroller): RandonneurCheckPointRepresentation {
		if (index === 0) {
			return kontroller.at(0
			)
		} else {
			return kontroller.at(index - 1
			);
		}
	}

	private nextIndexForward(index, kontroller): RandonneurCheckPointRepresentation {
		if (index === kontroller.length) {
			return kontroller.at(kontroller.length
			);
		} else {
			return kontroller.at(index + 1
			);
		}
	}

	scroll(id) {
		let el = document.getElementById(id);
		if (el) {
			el.scrollIntoView({behavior: 'smooth'});
		}

	}

	dnfSubject = new BehaviorSubject(false);
	isdnf$ = this.dnfSubject.asObservable().pipe(
		map((val) => {
			if (val === true) {
				this.dnfknapptext = 'Undo'
			} else {
				this.dnfknapptext = 'ABANDON BREVET'
			}
			return val;
		})
	).subscribe((dnf) => {
		console.log(dnf);
	});


	dnf($event: any, s: RandonneurCheckPointRepresentation) {
		this.comp.setDnf($event, s);
	}

	async dnf2(s: RandonneurCheckPointRepresentation) {
		await this.comp.dnfLinkExists(s).then(async (res) => {
			if (!res) {
				await this.comp.setDnf(false, s);
				this.dnfSubject.next(true);
			} else {
				await this.comp.setDnf(true, s);
				this.dnfSubject.next(false);
			}
		})

	}

	nextISSceret(s: RandonneurCheckPointRepresentation) {

		if (!s) {
			return false;
		}

		return s.checkpoint.site.adress === '-' || s.checkpoint.site.place.toLowerCase() === 'secret' || s.checkpoint.site.adress.toLowerCase() === 'hemlig';

	}

	ngOnInit(): void {

		this.comp.reload();
		this.getGeoLocation();
	}

	ngAfterViewInit(): void {
		this.scroll(null)
	}
}
