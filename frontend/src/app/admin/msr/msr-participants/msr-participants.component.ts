import { Component, OnInit, OnDestroy, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { MsrStatsService, MsrEvent, Participant, ParticipantsResponse } from '../../../shared/services/msr-stats.service';
import { TranslationService } from '../../../core/services/translation.service';
import { timeout, catchError, takeUntil, debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { of, Subject } from 'rxjs';

@Component({
  selector: 'app-msr-participants',
  templateUrl: './msr-participants.component.html',
  styleUrls: ['./msr-participants.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MsrParticipantsComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();
  private searchSubject$ = new Subject<string>();

  participants: Participant[] = [];
  loading = false;
  error: string | null = null;

  // MSR Events dropdown
  msrEvents: MsrEvent[] = [];
  selectedEventUid: string = '';
  loadingEvents = false;
  eventsError: string | null = null;

  // Search and filter
  searchTerm: string = '';
  filterReservation: string = 'all'; // 'all', 'confirmed', 'reservation'
  filterOptionalProduct: string = 'all'; // 'all' or specific product name
  availableProducts: string[] = [];
  filteredParticipants: Participant[] = [];

  constructor(
    private msrStatsService: MsrStatsService,
    private cdr: ChangeDetectorRef,
    private translationService: TranslationService
  ) { }

  ngOnInit(): void {
    this.loadMsrEvents();
    this.setupSearchDebouncing();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
    this.searchSubject$.complete();
  }

  private setupSearchDebouncing(): void {
    this.searchSubject$
      .pipe(
        debounceTime(300),
        distinctUntilChanged(),
        takeUntil(this.destroy$)
      )
      .subscribe(searchTerm => {
        this.searchTerm = searchTerm;
        this.applyFilters();
      });
  }

  loadMsrEvents(): void {
    console.log('MsrParticipantsComponent: Loading MSR events...');
    this.loadingEvents = true;
    this.eventsError = null;

    this.msrStatsService.getMsrEvents()
      .pipe(
        timeout(10000),
        takeUntil(this.destroy$),
        catchError(error => {
          console.error('MsrParticipantsComponent: Error loading MSR events:', error);
          return of(null);
        })
      )
      .subscribe({
        next: (response) => {
          if (response) {
            this.msrEvents = response.data;
            console.log('MsrParticipantsComponent: Loaded', this.msrEvents.length, 'MSR events');

            // Auto-select first event if available
            if (this.msrEvents.length > 0) {
              this.selectedEventUid = this.msrEvents[0].event_uid;
              console.log('MsrParticipantsComponent: Auto-selected first event:', this.selectedEventUid);
            }
          }
          this.loadingEvents = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          console.error('MsrParticipantsComponent: Error loading MSR events:', error);
          this.eventsError = this.translationService.translate('msr.errorLoadingEvents');
          this.loadingEvents = false;
          this.cdr.markForCheck();
        }
      });
  }

  loadParticipants(): void {
    if (!this.selectedEventUid) {
      this.error = this.translationService.translate('msr.selectEvent') + '.';
      return;
    }

    console.log('MsrParticipantsComponent: Loading participants for event:', this.selectedEventUid);
    this.loading = true;
    this.error = null;

    this.msrStatsService.getEventParticipants(this.selectedEventUid)
      .pipe(
        timeout(10000),
        takeUntil(this.destroy$),
        catchError(error => {
          console.error('MsrParticipantsComponent: Error loading participants:', error);
          return of(null);
        })
      )
      .subscribe({
        next: (response) => {
          if (response) {
            this.participants = response.registrations;
            this.extractAvailableProducts();
            this.applyFilters();
            console.log('MsrParticipantsComponent: Loaded', this.participants.length, 'participants');
          }
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          console.error('MsrParticipantsComponent: Error loading participants:', error);
          this.error = this.translationService.translate('msr.errorLoadingParticipants');
          this.loading = false;
          this.cdr.markForCheck();
        }
      });
  }

  onEventChange(): void {
    console.log('MsrParticipantsComponent: Event changed to:', this.selectedEventUid);
    // Clear previous data when event changes
    this.participants = [];
    this.filteredParticipants = [];
    this.availableProducts = [];
    this.filterOptionalProduct = 'all';
    this.error = null;
  }

  extractAvailableProducts(): void {
    const productSet = new Set<string>();
    this.participants.forEach(participant => {
      participant.optional_products.forEach(product => {
        productSet.add(product.product_name);
      });
    });
    this.availableProducts = Array.from(productSet).sort();
    console.log('MsrParticipantsComponent: Available products:', this.availableProducts);
  }

  applyFilters(): void {
    let filtered = [...this.participants];

    // Apply search filter
    if (this.searchTerm.trim()) {
      const searchLower = this.searchTerm.toLowerCase();
      filtered = filtered.filter(participant =>
        participant.person.firstname.toLowerCase().includes(searchLower) ||
        participant.person.surname.toLowerCase().includes(searchLower) ||
        participant.person.email.toLowerCase().includes(searchLower)
      );
    }

    // Apply reservation filter
    if (this.filterReservation === 'confirmed') {
      filtered = filtered.filter(participant => !participant.reservation);
    } else if (this.filterReservation === 'reservation') {
      filtered = filtered.filter(participant => participant.reservation);
    }

    // Apply optional products filter
    if (this.filterOptionalProduct !== 'all') {
      filtered = filtered.filter(participant =>
        participant.optional_products.some(product =>
          product.product_name === this.filterOptionalProduct
        )
      );
    }

    this.filteredParticipants = filtered;
  }

  onSearchChange(searchTerm: string): void {
    this.searchSubject$.next(searchTerm);
  }

  onFilterChange(): void {
    this.applyFilters();
  }

  onOptionalProductFilterChange(): void {
    this.applyFilters();
  }

  getSelectedEventTitle(): string {
    const selectedEvent = this.msrEvents.find(event => event.event_uid === this.selectedEventUid);
    return selectedEvent ? selectedEvent.title : 'Välj evenemang';
  }

  exportToCsv(): void {
    if (this.filteredParticipants.length === 0) {
      return;
    }

    const headers = [
      this.translationService.translate('msr.name'),
      this.translationService.translate('msr.email'),
      this.translationService.translate('msr.registrationDate'),
      this.translationService.translate('msr.status'),
      this.translationService.translate('msr.optionalProducts')
    ];
    const csvContent = [
      // Add metadata about the export
      `"${this.translationService.translate('msr.exportedFrom')} MSR ${this.translationService.translate('msr.participants')} - ${this.getSelectedEventTitle()}"`,
      `"${this.translationService.translate('msr.exportedAt')}: ${new Date().toLocaleString('sv-SE')}"`,
      `"${this.translationService.translate('msr.numberOfParticipants')}: ${this.filteredParticipants.length} ${this.translationService.translate('msr.of')} ${this.participants.length}"`,
      this.getFilterDescription(),
      '', // Empty line
      headers.join(','),
      ...this.filteredParticipants.map(participant => [
        `"${participant.person.firstname} ${participant.person.surname}"`,
        `"${participant.person.email}"`,
        `"${participant.created_at}"`,
        participant.reservation ? 'Reservation' : 'Bekräftad',
        `"${participant.optional_products.map(p => p.product_name).join('; ')}"`
      ].join(','))
    ].join('\n');

    // Generate filename based on filters
    const filename = this.generateCsvFilename();

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  getCsvFilenamePreview(): string {
    if (this.filteredParticipants.length === 0) {
      return 'Inga deltagare att exportera';
    }
    return `Kommer att sparas som: ${this.generateCsvFilename()}`;
  }

  private getFilterDescription(): string {
    const filters = [];

    if (this.searchTerm.trim()) {
      filters.push(`Sök: "${this.searchTerm}"`);
    }

    if (this.filterReservation !== 'all') {
      filters.push(`Status: ${this.filterReservation === 'confirmed' ? 'Bekräftade registreringar' : 'Reservationer'}`);
    }

    if (this.filterOptionalProduct !== 'all') {
      filters.push(`Valfri produkt: ${this.filterOptionalProduct}`);
    }

    if (filters.length === 0) {
      return '"Filtrering: Ingen"';
    }

    return `"Filtrering: ${filters.join(', ')}"`;
  }

  private generateCsvFilename(): string {
    const eventTitle = this.getSelectedEventTitle().replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
    const timestamp = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
    const parts = [`msr-${eventTitle}-${timestamp}`];

    // Add filter indicators
    if (this.searchTerm.trim()) {
      parts.push(`search-${this.searchTerm.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase()}`);
    }

    if (this.filterReservation !== 'all') {
      parts.push(this.filterReservation === 'confirmed' ? 'confirmed' : 'reservations');
    }

    if (this.filterOptionalProduct !== 'all') {
      const productName = this.filterOptionalProduct.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
      parts.push(`product-${productName}`);
    }

    // Add count if filtered
    if (this.filteredParticipants.length !== this.participants.length) {
      parts.push(`${this.filteredParticipants.length}-of-${this.participants.length}`);
    }

    return `${parts.join('-')}.csv`;
  }
}
