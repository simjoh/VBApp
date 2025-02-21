import {EventRepresentation, SiteRepresentation} from "./api";


export interface META {
  API_CONTACT: string;
  API_VERSION: string;
  ERROR: string;
}


export interface EVENT {
  EVENT_DISTANCE_KM: number;
  EVENT_DISTANCE_MILE: number;
  ROUTE_DISTANCE_KM: number;
  ROUTE_DISTANCE_MILE: number;
  MAX_TIME: string;
  MIN_TIME: string;
  START_DATE: string;
  START_TIME: string;
  START_DATE_PRINTABLE: string;
  GRAVEL_DISTANCE_KM: number;
  GRAVEL_DISTANCE_MILE: number
  GRAVEL_PERCENT: number;
  GRAVEL_EXTRA_TIME: string;
  GRAVEL_MAX_TIME: string;
  CALC_METHOD: string;
}

export interface CONTROL_ITEM {
  CONTROL_NUMBER: number;
  CONTROL_NAME: string;
  CONTROL_META_NAME: string;
  CONTROL_DISTANCE_KM: number;
  CONTROL_DISTANCE_MILE: number
  OPEN: string;
  CLOSE: string;
  RELATIVE_OPEN: string;
  RELATIVE_CLOSE: string;
  GRAVEL_CLOSE: string;
  GRAVEL_CLOSE_RELATIVE: string;
}

export interface CONTROLS {
  items: CONTROL_ITEM[];
}

export interface RusaTimeRepresentation {
  meta: META;
  controls: CONTROLS;
  event: EVENT;
}

export interface RusaPlannerControlInputRepresentation {
  DISTANCE: number;
  SITE: string;
}

export interface RusaPlannerInputRepresentation {
  controls: RusaPlannerControlInputRepresentation[];
  event_distance: number;
  start_date: string;
  start_time: string;
  event_uid: string;
  track_title: string;
  link: string;
}

export interface RusaControlResponseRepresentation {
  rusaControlRepresentation: CONTROL_ITEM;
  siteRepresentation: SiteRepresentation;

}

export interface RusaTrackRepresentation {
  EVENT_DISTANCE_KM: number;
  EVENT_DISTANCE_MILE: number;
  ROUTE_DISTANCE_KM: number;
  ROUTE_DISTANCE_MILE: number;
  MAX_TIME: string;
  MIN_TIME: string;
  START_DATE: string;
  START_TIME: string;
  START_DATE_PRINTABLE: string;
  GRAVEL_DISTANCE_KM: number;
  GRAVEL_DISTANCE_MILE: number;
  GRAVEL_PERCENT: number;
  GRAVEL_EXTRA_TIME: string;
  GRAVEL_MAX_TIME: string;
  CALC_METHOD: string;
  TRACK_TITLE: string;
  LINK_TO_TRACK: string;
}

export interface RusaPlannerResponseRepresentation {
  rusaMetaRepresentation: META;
  eventRepresentation: EventRepresentation;
  rusaTrackRepresentation: RusaTrackRepresentation;
  rusaplannercontrols: Array<RusaControlResponseRepresentation>;
}




