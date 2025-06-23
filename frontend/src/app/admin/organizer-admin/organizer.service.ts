import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { map, tap, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';

export interface OrganizerRepresentation {
  id: number;
  organization_name: string;
  description?: string;
  website?: string;
  website_pay?: string;
  logo_svg?: string;
  contact_person_name: string;
  email: string;
  active: boolean;
  club_uid?: string;
  links?: any[];
}

@Injectable({
  providedIn: 'root'
})
export class OrganizerService {
  private organizersSubject = new BehaviorSubject<OrganizerRepresentation[]>([]);
  public organizers$ = this.organizersSubject.asObservable();

  constructor(private http: HttpClient) {
    this.loadOrganizers();
  }

  getOrganizers(): Observable<OrganizerRepresentation[]> {
    return this.organizers$;
  }

  getOrganizerById(id: number): Observable<OrganizerRepresentation> {
    if (!id || id === undefined || id === null) {
      throw new Error('Organizer ID is required and cannot be undefined');
    }
    return this.http.get<OrganizerRepresentation>(`${environment.backend_url}organizer/${id}`);
  }

  createOrganizer(organizer: OrganizerRepresentation): Observable<OrganizerRepresentation> {
    const { id, ...organizerData } = organizer;
    return this.http.post<OrganizerRepresentation>(`${environment.backend_url}organizer/createorganizer`, organizerData)
      .pipe(
        tap(newOrganizer => {
          const currentOrganizers = this.organizersSubject.value;
          this.organizersSubject.next([...currentOrganizers, newOrganizer]);
        })
      );
  }

  updateOrganizer(id: number, organizer: OrganizerRepresentation): Observable<OrganizerRepresentation> {
    if (!id || id === undefined || id === null) {
      throw new Error('Organizer ID is required and cannot be undefined');
    }
    const { ...organizerData } = organizer;
    return this.http.put<OrganizerRepresentation>(`${environment.backend_url}organizer/${id}`, organizerData)
      .pipe(
        tap(() => {
          this.loadOrganizers();
        })
      );
  }

  deleteOrganizer(id: number): Observable<any> {
    if (!id || id === undefined || id === null) {
      throw new Error('Organizer ID is required and cannot be undefined');
    }
    return this.http.delete(`${environment.backend_url}organizer/${id}`)
      .pipe(
        tap(() => {
          this.loadOrganizers();
        })
      );
  }

  public loadOrganizers(): void {
    this.http.get<OrganizerRepresentation[]>(`${environment.backend_url}organizer/allorganizers`)
      .pipe(
        catchError(error => {
          console.error('Error loading organizers:', error);
          return of([]);
        })
      )
      .subscribe(organizers => this.organizersSubject.next(organizers));
  }
}
