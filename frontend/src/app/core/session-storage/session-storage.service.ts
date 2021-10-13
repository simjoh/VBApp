import {Injectable} from '@angular/core';

@Injectable({
	providedIn: 'root'
})
export class SessionStorageService {
	sessionStorage: Storage;

	constructor(private window: Window) {
		this.sessionStorage = this.window.sessionStorage;
	}

	get(key: string): any {
		if (this.isSessionStorageSupported) {
			return JSON.parse(<string>this.sessionStorage.getItem(key));
		}
		return null;
	}

	set(key: string, value: any): boolean {
		if (this.isSessionStorageSupported) {
			this.sessionStorage.setItem(key, JSON.stringify(value));
			return true;
		}
		return false;
	}

	remove(key: string): boolean {
		if (this.isSessionStorageSupported) {
			this.sessionStorage.removeItem(key);
			return true;
		}
		return false;
	}

	get isSessionStorageSupported(): boolean {
		return !!this.sessionStorage;
	}

}
