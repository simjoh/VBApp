export interface Position {
	coords: {
		latitude: number;
		longitude: number;
		altitude: number | null;
		accuracy: number;
		altitudeAccuracy: number | null;
		heading: number | null;
		speed: number | null;
	};
	timestamp: number;
}
