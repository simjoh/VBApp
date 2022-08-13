


export interface META {
  API_CONTACT: string;
  API_VERSION: string;
  ERROR: string;
}


export interface EVENT {
  CALC_METHOD: string;

}

export interface CONTROL_ITEM {
  CONTROL_NUMBER: number;

}

export interface CONTROLS {
  items: CONTROL_ITEM[];
}

export interface RusaTimeRepresentation {
  meta: META;
  controls: CONTROLS;
  event: EVENT;
}


