import { Component, OnInit, Output, EventEmitter, Input, ChangeDetectionStrategy, ChangeDetectorRef, forwardRef } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { Observable, BehaviorSubject } from 'rxjs';
import { map, catchError, tap } from 'rxjs/operators';
import { of } from 'rxjs';
import { OrganizerService, OrganizerRepresentation } from '../../admin/organizer-admin/organizer.service';

export interface Organizer {
  id: number;
  name: string;
  email?: string;
  contact_person?: string;
  logo_svg?: string;
  website?: string;
  description?: string;
}

@Component({
  selector: 'brevet-organizer-selector',
  templateUrl: './organizer-selector.component.html',
  styleUrls: ['./organizer-selector.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => OrganizerSelectorComponent),
      multi: true
    }
  ]
})
export class OrganizerSelectorComponent implements OnInit, ControlValueAccessor {
  @Input() selectedOrganizerId: number | null = null;
  @Input() placeholder: string = 'Välj arrangör';
  @Input() disabled: boolean = false;
  @Input() showPreview: boolean = true;
  @Output() organizerChange = new EventEmitter<number>();
  @Output() organizerObjectChange = new EventEmitter<OrganizerRepresentation | null>();

  private static organizersCache: Organizer[] | null = null;
  private static cacheTimestamp: number = 0;
  private static readonly CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

  organizers: Organizer[] = [];
  selectedOrganizer: Organizer | null = null;
  loading: boolean = false;
  error: string | null = null;

  // ControlValueAccessor properties
  private onChange = (value: number | null) => {};
  private onTouched = () => {};

  constructor(
    private organizerService: OrganizerService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
    this.loadOrganizers();
  }

  // ControlValueAccessor implementation
  writeValue(value: number | null): void {
    this.selectedOrganizerId = value;
    this.updateSelectedOrganizer();
    this.cdr.markForCheck();
  }

  registerOnChange(fn: (value: number | null) => void): void {
    this.onChange = fn;
  }

  registerOnTouched(fn: () => void): void {
    this.onTouched = fn;
  }

  setDisabledState(isDisabled: boolean): void {
    this.disabled = isDisabled;
    this.cdr.markForCheck();
  }

  private loadOrganizers(): void {
    // Check cache first
    if (this.isCacheValid()) {
      this.organizers = OrganizerSelectorComponent.organizersCache!;
      this.updateSelectedOrganizer();
      this.cdr.markForCheck();
      return;
    }

    this.loading = true;
    this.error = null;

    this.organizerService.getOrganizers().subscribe({
      next: (organizers) => {
        this.organizers = organizers.map(org => ({
          id: org.id,
          name: org.organization_name,
          email: org.email,
          contact_person: org.contact_person_name,
          logo_svg: org.logo_svg,
          website: org.website,
          description: org.description
        }));

        // Cache the organizers
        OrganizerSelectorComponent.organizersCache = this.organizers;
        OrganizerSelectorComponent.cacheTimestamp = Date.now();

        this.loading = false;
        this.updateSelectedOrganizer();
        this.cdr.markForCheck();
      },
      error: (error) => {
        console.error('Error loading organizers:', error);
        this.error = 'Kunde inte ladda arrangörer';
        this.loading = false;
        this.cdr.markForCheck();
      }
    });
  }

  private isCacheValid(): boolean {
    return OrganizerSelectorComponent.organizersCache !== null &&
           (Date.now() - OrganizerSelectorComponent.cacheTimestamp) < OrganizerSelectorComponent.CACHE_DURATION;
  }

  private updateSelectedOrganizer(): void {
    if (this.selectedOrganizerId && this.organizers.length > 0) {
      this.selectedOrganizer = this.organizers.find(org => org.id === this.selectedOrganizerId) || null;
    }
  }

  onOrganizerChange(event: any): void {
    const organizerId = event.value;
    this.selectedOrganizerId = organizerId;
    this.selectedOrganizer = this.organizers.find(org => org.id === organizerId) || null;

    // Call ControlValueAccessor callbacks
    this.onChange(organizerId);
    this.onTouched();

    this.organizerChange.emit(organizerId);

    // Find the original organizer object from the service to emit
    if (this.selectedOrganizer) {
      this.organizerService.getOrganizers().subscribe(organizers => {
        const originalOrganizer = organizers.find(org => org.id === organizerId);
        this.organizerObjectChange.emit(originalOrganizer || null);
      });
    } else {
      this.organizerObjectChange.emit(null);
    }

    this.cdr.markForCheck();
  }

  refreshOrganizers(): void {
    // Clear cache and reload
    OrganizerSelectorComponent.organizersCache = null;
    this.loadOrganizers();
  }

  get selectedOrganizerForTemplate(): Organizer | null {
    if (!this.selectedOrganizerId || !this.organizers.length) return null;
    return this.organizers.find(org => org.id === this.selectedOrganizerId) || null;
  }

  getSafeLogoUrl(logoSvg: string | undefined): string {
    if (!logoSvg) return '';

    // If it's already a data URL, return as is
    if (logoSvg.startsWith('data:image/svg+xml')) {
      return logoSvg;
    }

    // If it's base64 encoded SVG, create data URL
    if (logoSvg && !logoSvg.includes('<svg')) {
      return `data:image/svg+xml;base64,${logoSvg}`;
    }

    // If it's raw SVG, encode it properly handling Unicode characters
    if (logoSvg && logoSvg.includes('<svg')) {
      try {
        // Use encodeURIComponent to handle Unicode characters properly
        const encodedSvg = encodeURIComponent(logoSvg);
        return `data:image/svg+xml;charset=utf-8,${encodedSvg}`;
      } catch (error) {
        console.error('Error encoding SVG:', error);
        return '';
      }
    }

    return '';
  }
}
