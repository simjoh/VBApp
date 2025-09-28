import { Component, OnInit, OnDestroy, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { MsrStatsService, MsrEvent, NonParticipantOptionalsResponse, NonParticipantProduct } from '../../../shared/services/msr-stats.service';
import { TranslationService } from '../../../core/services/translation.service';
import { of, Subject } from 'rxjs';
import { catchError, timeout, takeUntil, debounceTime, distinctUntilChanged } from 'rxjs/operators';

@Component({
  selector: 'app-msr-non-participant-optionals',
  templateUrl: './msr-non-participant-optionals.component.html',
  styleUrls: ['./msr-non-participant-optionals.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class MsrNonParticipantOptionalsComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();
  private searchSubject$ = new Subject<string>();

  msrEvents: MsrEvent[] = [];
  selectedEventUid: string = '';
  loadingEvents = false;
  eventsError: string | null = null;

  // Date filtering
  startDate: Date | null = null;
  endDate: Date | null = null;
  filterType: 'event' | 'date' = 'event';

  // Data
  optionalsData: NonParticipantOptionalsResponse | null = null;
  loading = false;
  error: string | null = null;

  // Search and filter
  searchTerm: string = '';
  selectedProductId: number | null = null;
  availableProducts: NonParticipantProduct[] = [];
  filteredProducts: NonParticipantProduct[] = [];

  constructor(
    private msrStatsService: MsrStatsService,
    private cdr: ChangeDetectorRef,
    private translationService: TranslationService
  ) { }

  ngOnInit(): void {
    this.loadMsrEvents();
    this.setDefaultDateRange();
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

  setDefaultDateRange(): void {
    const now = new Date();
    const currentYear = now.getFullYear();
    this.startDate = new Date(currentYear, 0, 1); // January 1st
    this.endDate = new Date(currentYear, 11, 31); // December 31st
  }

  loadMsrEvents(): void {
    console.log('MsrNonParticipantOptionalsComponent: Loading MSR events...');
    this.loadingEvents = true;
    this.eventsError = null;

    this.msrStatsService.getMsrEvents()
      .pipe(
        timeout(10000),
        takeUntil(this.destroy$),
        catchError(error => {
          console.error('MsrNonParticipantOptionalsComponent: Error loading MSR events:', error);
          return of(null);
        })
      )
      .subscribe({
        next: (response) => {
          if (response) {
            this.msrEvents = response.data;
            console.log('MsrNonParticipantOptionalsComponent: Loaded', this.msrEvents.length, 'MSR events');

            if (this.msrEvents.length > 0) {
              this.selectedEventUid = this.msrEvents[0].event_uid;
              console.log('MsrNonParticipantOptionalsComponent: Auto-selected first event:', this.selectedEventUid);
            }
          }
          this.loadingEvents = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          console.error('MsrNonParticipantOptionalsComponent: Error loading MSR events:', error);
          this.eventsError = this.translationService.translate('msr.errorLoadingEvents');
          this.loadingEvents = false;
          this.cdr.markForCheck();
        }
      });
  }

  loadOptionals(): void {
    if (this.filterType === 'event' && !this.selectedEventUid) {
      this.error = this.translationService.translate('msr.selectEvent') + '.';
      return;
    }

    if (this.filterType === 'date' && (!this.startDate || !this.endDate)) {
      this.error = this.translationService.translate('msr.startDate') + ' ' + this.translationService.translate('common.and') + ' ' + this.translationService.translate('msr.endDate') + '.';
      return;
    }

    console.log('MsrNonParticipantOptionalsComponent: Loading non-participant optionals...');
    this.loading = true;
    this.error = null;

    const params: any = {};
    if (this.filterType === 'event') {
      params.event_uid = this.selectedEventUid;
    } else {
      // Convert Date objects to yyyy-MM-dd format
      params.start_date = this.startDate ? this.formatDateForApi(this.startDate) : null;
      params.end_date = this.endDate ? this.formatDateForApi(this.endDate) : null;
    }

    this.msrStatsService.getNonParticipantOptionals(params)
      .pipe(
        timeout(10000),
        takeUntil(this.destroy$),
        catchError(error => {
          console.error('MsrNonParticipantOptionalsComponent: Error loading optionals:', error);
          return of(null);
        })
      )
      .subscribe({
        next: (response) => {
          if (response) {
            this.optionalsData = response;
            this.availableProducts = response.products;
            console.log('MsrNonParticipantOptionalsComponent: Loaded', response.total_registrations, 'registrations');
            console.log('MsrNonParticipantOptionalsComponent: Available products:', this.availableProducts.length);

            // Reset product filter if the selected product is not in the new data
            if (this.selectedProductId !== null) {
              const selectedId = Number(this.selectedProductId);
              const productExists = this.availableProducts.some(p => Number(p.product_id) === selectedId);
              if (!productExists) {
                console.log('MsrNonParticipantOptionalsComponent: Selected product not found in new data, resetting filter');
                this.selectedProductId = null;
              }
            }

            this.applyFilters();
          }
          this.loading = false;
          this.cdr.markForCheck();
        },
        error: (error) => {
          console.error('MsrNonParticipantOptionalsComponent: Error loading optionals:', error);
          this.error = this.translationService.translate('msr.errorLoadingOptionals');
          this.loading = false;
          this.cdr.markForCheck();
        }
      });
  }

  onFilterTypeChange(): void {
    console.log('MsrNonParticipantOptionalsComponent: Filter type changed to:', this.filterType);
    // Clear previous data when filter type changes
    this.optionalsData = null;
    this.availableProducts = [];
    this.filteredProducts = [];
    this.selectedProductId = null;
    this.error = null;
  }

  onEventChange(): void {
    console.log('MsrNonParticipantOptionalsComponent: Event changed to:', this.selectedEventUid);
    // Clear previous data when event changes
    this.optionalsData = null;
    this.availableProducts = [];
    this.filteredProducts = [];
    this.selectedProductId = null;
    this.error = null;
  }

  onDateChange(): void {
    console.log('MsrNonParticipantOptionalsComponent: Date range changed');
    // Clear previous data when date changes
    this.optionalsData = null;
    this.availableProducts = [];
    this.filteredProducts = [];
    this.selectedProductId = null;
    this.error = null;
  }

  applyFilters(): void {
    if (!this.optionalsData) {
      this.filteredProducts = [];
      return;
    }

    let filtered = [...this.optionalsData.products];
    console.log('MsrNonParticipantOptionalsComponent: Starting with', filtered.length, 'products');

    // Apply search filter
    if (this.searchTerm.trim()) {
      const searchLower = this.searchTerm.toLowerCase();
      filtered = filtered.filter(product =>
        product.product_name.toLowerCase().includes(searchLower) ||
        product.description.toLowerCase().includes(searchLower) ||
        product.registrations.some(reg =>
          reg.firstname.toLowerCase().includes(searchLower) ||
          reg.surname.toLowerCase().includes(searchLower) ||
          reg.email.toLowerCase().includes(searchLower)
        )
      );
      console.log('MsrNonParticipantOptionalsComponent: After search filter:', filtered.length, 'products');
    }

    // Apply product filter
    if (this.selectedProductId !== null) {
      const selectedId = Number(this.selectedProductId);
      console.log('MsrNonParticipantOptionalsComponent: Filtering by product ID:', this.selectedProductId, '(converted to:', selectedId, ')');
      filtered = filtered.filter(product => Number(product.product_id) === selectedId);
      console.log('MsrNonParticipantOptionalsComponent: After product filter:', filtered.length, 'products');
    }

    this.filteredProducts = filtered;
    console.log('MsrNonParticipantOptionalsComponent: Final filtered products:', this.filteredProducts.length);
  }

  onSearchChange(searchTerm: string): void {
    this.searchSubject$.next(searchTerm);
  }

  onProductFilterChange(): void {
    console.log('MsrNonParticipantOptionalsComponent: Product filter changed to:', this.selectedProductId);
    console.log('MsrNonParticipantOptionalsComponent: Available products:', this.availableProducts.length);
    this.applyFilters();
  }

  getSelectedEventTitle(): string {
    const selectedEvent = this.msrEvents.find(event => event.event_uid === this.selectedEventUid);
    return selectedEvent ? selectedEvent.title : this.translationService.translate('msr.selectEvent');
  }

  getFilterDescription(): string {
    if (this.filterType === 'event') {
      return `${this.translationService.translate('msr.event')}: ${this.getSelectedEventTitle()}`;
    } else {
      const startStr = this.startDate ? this.formatDateForApi(this.startDate) : this.translationService.translate('common.none');
      const endStr = this.endDate ? this.formatDateForApi(this.endDate) : this.translationService.translate('common.none');
      return `${this.translationService.translate('msr.filterByDate')}: ${startStr} - ${endStr}`;
    }
  }

  exportToCsv(): void {
    if (!this.optionalsData || this.filteredProducts.length === 0) {
      return;
    }

    const headers = [
      this.translationService.translate('msr.product'),
      this.translationService.translate('msr.name'),
      this.translationService.translate('msr.email'),
      this.translationService.translate('msr.quantity'),
      this.translationService.translate('msr.additionalInfo'),
      this.translationService.translate('msr.registrationDate'),
      this.translationService.translate('msr.event')
    ];
    const rows: string[][] = [];

    this.filteredProducts.forEach(product => {
      product.registrations.forEach(reg => {
        rows.push([
          product.product_name,
          `"${reg.firstname} ${reg.surname}"`,
          `"${reg.email}"`,
          reg.quantity.toString(),
          `"${reg.additional_information || ''}"`,
          `"${reg.created_at}"`,
          `"${reg.event_title}"`
        ]);
      });
    });

    const csvContent = [
      // Add metadata about the export
      `"${this.translationService.translate('msr.exportedFrom')} MSR ${this.translationService.translate('msr.optionalProducts')} - ${this.getFilterDescription()}"`,
      `"${this.translationService.translate('msr.exportedAt')}: ${new Date().toLocaleString('sv-SE')}"`,
      `"${this.translationService.translate('msr.numberOfRegistrations')}: ${this.optionalsData.total_registrations}"`,
      `"${this.translationService.translate('msr.numberOfProducts')}: ${this.filteredProducts.length}"`,
      '', // Empty line
      headers.join(','),
      ...rows.map(row => row.join(','))
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
    if (!this.optionalsData || this.filteredProducts.length === 0) {
      return this.translationService.translate('msr.csvExportNoData');
    }
    return `${this.translationService.translate('msr.csvExportTooltip')} ${this.generateCsvFilename()}`;
  }

  getSelectedProductName(): string {
    if (this.selectedProductId === null) {
      return this.translationService.translate('msr.allProducts');
    }

    // Convert both to numbers for comparison to handle string/number mismatches
    const selectedId = Number(this.selectedProductId);

    // First try to find in availableProducts
    let product = this.availableProducts.find(p => Number(p.product_id) === selectedId);

    // If not found, try to find in the original data
    if (!product && this.optionalsData) {
      product = this.optionalsData.products.find(p => Number(p.product_id) === selectedId);
    }

    // If still not found, check if we have any products at all
    if (!product) {
      console.log('MsrNonParticipantOptionalsComponent: Product not found for ID:', this.selectedProductId, '(converted to:', selectedId, ')');
      console.log('MsrNonParticipantOptionalsComponent: Available products:', this.availableProducts.map(p => ({ id: p.product_id, name: p.product_name })));
      return this.translationService.translate('msr.productNotFound');
    }

    return product.product_name;
  }

  private formatDateForApi(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  private generateCsvFilename(): string {
    const timestamp = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
    const parts = [`msr-non-participant-optionals-${timestamp}`];

    if (this.filterType === 'event') {
      const eventTitle = this.getSelectedEventTitle().replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
      parts.push(eventTitle);
    } else {
      const startStr = this.startDate ? this.formatDateForApi(this.startDate) : 'no-start';
      const endStr = this.endDate ? this.formatDateForApi(this.endDate) : 'no-end';
      parts.push(`${startStr}-to-${endStr}`);
    }

    // Add search filter if applied
    if (this.searchTerm.trim()) {
      parts.push(`search-${this.searchTerm.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase()}`);
    }

    // Add product filter if applied
    if (this.selectedProductId !== null) {
      const product = this.availableProducts.find(p => p.product_id === this.selectedProductId);
      if (product) {
        const productName = product.product_name.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
        parts.push(`product-${productName}`);
      }
    }

    // Add count if filtered
    if (this.filteredProducts.length !== this.optionalsData?.products.length) {
      parts.push(`${this.filteredProducts.length}-of-${this.optionalsData?.products.length}`);
    }

    return `${parts.join('-')}.csv`;
  }
}
