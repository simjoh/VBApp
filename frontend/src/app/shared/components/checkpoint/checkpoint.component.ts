import {ChangeDetectionStrategy, Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {RandonneurCheckPointRepresentation} from "../../../shared/api/api";
import {BehaviorSubject} from "rxjs";
import {LinkService} from "../../../core/link.service";
import {map} from "rxjs/operators";
import {ConfirmationService} from 'primeng/api';

@Component({
    selector: 'brevet-checkpoint',
    templateUrl: './checkpoint.component.html',
    styleUrls: ['./checkpoint.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [ConfirmationService],
    standalone: false
})
export class CheckpointComponent implements OnInit {


	checkinknapptext: string;
	checkoutknapptext: string;
	dnfknapptext: string

	chekedinSubject = new BehaviorSubject(false);
	checkedin$ = this.chekedinSubject.asObservable().pipe(
		map((val) => {
			if (val === false) {
				this.checkinknapptext = 'CHECK IN'
			} else {
				if (this.lastCheckpoint){
					this.checkinknapptext = 'FINISH'
				} else {
					this.checkinknapptext = 'CHECKED IN'
				}

			}
			return val;
		})
	);

	checkotSubject = new BehaviorSubject(false);
	checkedout$ = this.checkotSubject.asObservable().pipe(
		map((val) => {
			if (val === false) {
				this.checkoutknapptext = 'CHECK OUT'
			} else {
				this.checkoutknapptext = 'UNDO CHECK OUT'
			}
			return val;
		})
	);

	knapptextcheckout$ = this.checkedout$.pipe(
		map((val) => {
			if (val === false) {

				return 'CHECK OUT'
			} else {

				return 'UNDO CHECK OUT'
			}
		}));


	dnfSubject = new BehaviorSubject(false);
	isdnf$ = this.dnfSubject.asObservable().pipe(
		map((val) => {
			if (val === true) {
				this.dnfknapptext = 'UNDO DNF'
			} else {
				this.dnfknapptext = 'DNF'
			}
			return val;
		})
	);

	@Input() checkpoints: RandonneurCheckPointRepresentation
	@Input() start: boolean
	@Input() finsih: boolean
	@Input() distance: any
	@Input() distancetonext: any
	@Input() preview: boolean
	@Input() nextIsSecret: boolean
	@Input() lastCheckpoint: boolean
	@Input() firstCheckpoint: boolean


	@Output() checkedin = new EventEmitter<any>();
	@Output() checkedout = new EventEmitter<any>();
	@Output() dnf = new EventEmitter<any>();

	constructor(private linkservice: LinkService, private confirmationService: ConfirmationService) {
	}


	ngOnInit(): void {

		if (this.preview === true) {
			this.chekedinSubject.next(false)
			this.dnfSubject.next(false)
			this.checkotSubject.next(false);
		} else {
			this.chekedinSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp') === false)
			this.dnfSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.dnf') === false)
			this.checkotSubject.next(this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.checkout') === false)
		}

	}

	checkin() {

		if (this.preview === true) {
			alert("Preview mode");
		}


		if (!this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp')) {
			this.checkedin.emit(false);
			this.chekedinSubject.next(false);
			// this.confirmationService.confirm({
			//   message: 'Do you want want to undo check in',
			//   accept: () => {
			//     this.checkedin.emit(false);
			//     this.chekedinSubject.next(false);
			//   }
			// });
		} else {
			this.checkedin.emit(true);
			// this.confirmationService.confirm({
			//   message: 'Do you want want to check in',
			//   accept: () => {
			//     this.checkedin.emit(true);
			//   }
			// });
		}
	}

	checkout() {
		if (!this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.stamp') && this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.checkout')) {
			this.checkedout.emit(true);
			this.checkotSubject.next(true);
			// this.confirmationService.confirm({
			//   message: 'Do you want want to undo check in',
			//   accept: () => {
			//     this.checkedin.emit(false);
			//     this.chekedinSubject.next(false);
			//   }
			// });
		} else {
			this.checkedout.emit(false);
			this.checkotSubject.next(false);
			// this.confirmationService.confirm({
			//   message: 'Do you want want to check in',
			//   accept: () => {
			//     this.checkedin.emit(true);
			//   }
			// });
		}
	}

	setdnf() {

		if (this.preview === true) {
			alert("Preview mode");
		}
		if (this.linkservice.exists(this.checkpoints.links, 'relation.randonneur.dnf')) {
			//  this.dnfSubject.next(true);
			this.dnf.emit(true);
		} else {
			// this.dnfSubject.next(false);
			this.dnf.emit(false);
		}
	}


	trimWhitespaces(s: string) {
		return s.replace('/ /gi', "")
	}


}
