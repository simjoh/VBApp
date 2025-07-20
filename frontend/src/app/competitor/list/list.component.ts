import {AfterViewInit, ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {CompetitorListComponentService} from "./competitor-list-component.service";
import {RandonneurCheckPointRepresentation} from "../../shared/api/api";
import {BehaviorSubject, firstValueFrom} from "rxjs";
import {map} from "rxjs/operators";
import {GeolocationService} from "../../shared/geolocation.service";
import {PendingRequestsService} from "../../core/pending-requests.service";

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

	constructor(private comp: CompetitorListComponentService, private geolocationService: GeolocationService, private pendingRequestsService: PendingRequestsService) {
		this.dnfknapptext = "test";
	}

	async getGeoLocation() {
		this.pendingRequestsService.increase();
		try {
			await this.sleep(500); // 3 second delay
			const position = await firstValueFrom(this.geolocationService.getCurrentPosition());
			const currentTimestamp = Date.now();
			const timeDifference = Math.abs(currentTimestamp - position.timestamp);
			const timeDifferenceInMinutes = timeDifference / (1000 * 60);
			if (timeDifferenceInMinutes <= 1) {
				this.within = true;
			} else {
				this.within = false;
			}
			this.lat = position.coords.latitude;
			this.long = position.coords.longitude;
		} finally {
			this.pendingRequestsService.decrease();
		}
	}

	sleep(ms) {
		return new Promise(resolve => setTimeout(resolve, ms));
	}


	async checkin($event: any, s: RandonneurCheckPointRepresentation, kontroller, index) {
		try {
			this.pendingRequestsService.increase();
			await this.getGeoLocation();
			if (!this.within) {
				while (!this.within) {
					await this.sleep(3000);
					await this.getGeoLocation();
				}
			}

			if ($event === true) {
				await this.comp.stamp($event, s, this.lat, this.long);
				let nextindex = this.nextIndexForward(index, kontroller)

			} else {
				await this.comp.rollbackStamp($event, s);
			}
		} finally {
			this.pendingRequestsService.decrease();
		}
	}

	async checkout($event: any, s: RandonneurCheckPointRepresentation, kontroller, index)
	{
		if ($event === true) {
			await this.comp.checkout($event, s);
			localStorage.setItem('nextcheckpoint', JSON.stringify(kontroller.at(index + 1).checkpoint.checkpoint_uid));
			let nextindex = this.nextIndexForward(index, kontroller)
			setTimeout(() => {
				this.scroll(nextindex.checkpoint.checkpoint_uid);
			}, 2000);
		} else {
			await this.comp.undocheckout($event, s);
			let nextindex = this.nextIndexBackward(index, kontroller)
			setTimeout(() => {
				this.scroll(nextindex.checkpoint.checkpoint_uid);
			}, 2000);
			localStorage.setItem('nextcheckpoint', JSON.stringify(kontroller.at(index).checkpoint.checkpoint_uid));
		}

	}

	async undocheckout($event: any, s: RandonneurCheckPointRepresentation, kontroller: Array<RandonneurCheckPointRepresentation>, i: number) {
		await this.comp.undocheckout($event, s);
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
		// DNF status updated
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

	async ngOnInit(): Promise<void> {
		await this.getGeoLocation();
		this.comp.reload();

	}

	ngAfterViewInit(): void {
		this.scroll(null)
	}


}
