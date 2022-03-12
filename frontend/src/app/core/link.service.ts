import {Injectable} from '@angular/core';
import { Link } from '../shared/api/api';


@Injectable({
	providedIn: 'root'
})
export class LinkService {

	constructor() {
	}

	findByRel(a: Link[] | { link?: Link[] }, rel: string, method?: string): Link | null {
		if (typeof a !== 'undefined' && a !== null) {
			const array: Link[] = Array.isArray(a) ? a : a.link;
			if (array) {
				for (const link of array) {
					if (link.rel === rel && (!method || method === link.method)) {
						return link;
					}
				}
			}
		}
		return null;
	}

	/**
	 * Som ovan, men om flera länkar med samma rel finns så returneras alla matchande länkar
	 */
	findArrayByRel(a: any, rel: string, method?: string): Link[] {
		const matches = [];
		if (typeof a !== 'undefined' && a !== null) {
			const array = Array.isArray(a) ? a : a.link;
			if (array) {
				// eslint-disable-next-line @typescript-eslint/prefer-for-of
				for (let i = 0; i < array.length; i++) {
					const link = array[i];
					if (link.rel === rel && (!method || method === link.method)) {
						matches.push(link);
					}
				}
			}
		}
		return matches;
	}


	exists(a: Link[] | { link?: Link[] }, rel: string, method?: string) {
		return !!(this.findByRel(a, rel, method));
	}
	/**
	 * Returnerar en self-länk från objektet.
	 */
	self(o: any) {
		return this.findByRel(o, "self", "GET");
	}


}

