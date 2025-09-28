import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { tap, catchError, map } from 'rxjs/operators';
import { environment } from '../../../environments/environment';

export interface EventStats {
  event_uid: string;
  event_title: string;
  total_registrations: number;
  confirmed_registrations: string | number; // API returns string
  total_reservations: string | number; // API returns string
  max_registrations: number;
  registration_percentage: number;
  optional_products: OptionalProduct[];
  registration_trends: {
    last_7_days: string | number; // API returns string
    last_30_days: string | number; // API returns string
  };
}

export interface OptionalProduct {
  product_id: number;
  product_name: string;
  count: number;
  percentage: number;
}

export interface MsrEvent {
  event_uid: string;
  title: string;
  description: string;
  startdate: string;
  enddate: string;
  completed: boolean;
  event_type: string;
  organizer_id: number | null;
  county_id: number | null;
  event_group_uid: string | null;
  created_at: string;
  updated_at: string;
}

export interface MsrEventsResponse {
  event_type: string;
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
  data: MsrEvent[];
}

export interface Participant {
  registration_uid: string;
  reservation: boolean;
  created_at: string;
  person: {
    firstname: string;
    surname: string;
    email: string;
  };
  optional_products: {
    product_id: number;
    product_name: string;
  }[];
}

export interface ParticipantsResponse {
  event_uid: string;
  event_title: string;
  registrations: Participant[];
}

export interface NonParticipantOptional {
  optional_uid: string;
  firstname: string;
  surname: string;
  email: string;
  quantity: number;
  additional_information: string | null;
  created_at: string;
  event_title: string;
  event_startdate: string;
}

export interface NonParticipantProduct {
  product_id: number;
  product_name: string;
  description: string;
  price: number | null;
  total_quantity: number;
  total_registrations: number;
  registrations: NonParticipantOptional[];
}

export interface NonParticipantOptionalsResponse {
  filter_type: 'event' | 'date_interval';
  event_uid: string | null;
  start_date: string | null;
  end_date: string | null;
  total_registrations: number;
  total_quantity: number;
  products: NonParticipantProduct[];
}

@Injectable({
  providedIn: 'root'
})
export class MsrStatsService {
  private readonly apiUrl = environment.loppservice_url || '/loppservice/';
  private readonly apiKey = environment.loppservice_api_key || 'testkey';

  constructor(private http: HttpClient) {
    console.log('MsrStatsService: Environment loaded:', environment);
    console.log('MsrStatsService: API URL:', this.apiUrl);
    console.log('MsrStatsService: API Key:', this.apiKey);
  }

  getEventStats(eventUid: string): Observable<EventStats> {
    const headers = new HttpHeaders({
      'apikey': this.apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    });

    console.log('MsrStatsService: Making request to:', `${this.apiUrl}api/integration/event/${eventUid}/stats`);
    console.log('MsrStatsService: API Key:', this.apiKey);
    console.log('MsrStatsService: Headers:', headers);

    return this.http.get<any>(`${this.apiUrl}api/integration/event/${eventUid}/stats`, {
      headers,
      withCredentials: false
    }).pipe(
      tap(response => {
        console.log('MsrStatsService: Raw response received:', response);
        console.log('MsrStatsService: Response type:', typeof response);
        console.log('MsrStatsService: Response keys:', Object.keys(response || {}));
      }),
      map(response => {
        console.log('MsrStatsService: Mapping response to EventStats...');
        const mappedResponse: EventStats = {
          event_uid: response.event_uid,
          event_title: response.event_title,
          total_registrations: response.total_registrations,
          confirmed_registrations: response.confirmed_registrations,
          total_reservations: response.total_reservations,
          max_registrations: response.max_registrations,
          registration_percentage: response.registration_percentage,
          optional_products: response.optional_products || [],
          registration_trends: response.registration_trends || { last_7_days: 0, last_30_days: 0 }
        };
        console.log('MsrStatsService: Mapped response:', mappedResponse);
        return mappedResponse;
      }),
      catchError(error => {
        console.error('MsrStatsService: Error occurred:', error);
        return throwError(() => error);
      })
    );
  }

  getEventOptionalProducts(eventUid: string): Observable<any> {
    const headers = new HttpHeaders({
      'apikey': this.apiKey
    });

    return this.http.get(`${this.apiUrl}api/integration/event/${eventUid}/optional-products`, { headers });
  }

  getEventRegistrations(eventUid: string): Observable<any> {
    const headers = new HttpHeaders({
      'apikey': this.apiKey
    });

    return this.http.get(`${this.apiUrl}api/integration/event/${eventUid}/registrations`, { headers });
  }

  getMsrEvents(): Observable<MsrEventsResponse> {
    const headers = new HttpHeaders({
      'apikey': this.apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    });

    console.log('MsrStatsService: Fetching MSR events from:', `${this.apiUrl}api/integration/event/by-type?event_type=MSR`);

    return this.http.get<MsrEventsResponse>(`${this.apiUrl}api/integration/event/by-type?event_type=MSR`, {
      headers,
      withCredentials: false
    }).pipe(
      tap(response => {
        console.log('MsrStatsService: MSR events response received:', response);
        console.log('MsrStatsService: Found', response.total, 'MSR events');
      }),
      catchError(error => {
        console.error('MsrStatsService: Error fetching MSR events:', error);
        return throwError(() => error);
      })
    );
  }

  getEventParticipants(eventUid: string): Observable<ParticipantsResponse> {
    const headers = new HttpHeaders({
      'apikey': this.apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    });

    console.log('MsrStatsService: Fetching participants for event:', eventUid);

    return this.http.get<ParticipantsResponse>(`${this.apiUrl}api/integration/event/${eventUid}/registrations`, {
      headers,
      withCredentials: false
    }).pipe(
      tap(response => {
        console.log('MsrStatsService: Participants response received:', response);
        console.log('MsrStatsService: Found', response.registrations.length, 'participants');
      }),
      catchError(error => {
        console.error('MsrStatsService: Error fetching participants:', error);
        return throwError(() => error);
      })
    );
  }

  getNonParticipantOptionals(params: { event_uid?: string; start_date?: string; end_date?: string }): Observable<NonParticipantOptionalsResponse> {
    const headers = new HttpHeaders({
      'apikey': this.apiKey,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    });

    const queryParams = new URLSearchParams();
    if (params.event_uid) queryParams.set('event_uid', params.event_uid);
    if (params.start_date) queryParams.set('start_date', params.start_date);
    if (params.end_date) queryParams.set('end_date', params.end_date);

    const url = `${this.apiUrl}api/integration/non-participant-optionals?${queryParams.toString()}`;

    console.log('MsrStatsService: Fetching non-participant optionals:', params);

    return this.http.get<NonParticipantOptionalsResponse>(url, {
      headers,
      withCredentials: false
    }).pipe(
      tap(response => {
        console.log('MsrStatsService: Non-participant optionals response received:', response);
        console.log('MsrStatsService: Found', response.total_registrations, 'registrations across', response.products.length, 'products');
      }),
      catchError(error => {
        console.error('MsrStatsService: Error fetching non-participant optionals:', error);
        return throwError(() => error);
      })
    );
  }
}
