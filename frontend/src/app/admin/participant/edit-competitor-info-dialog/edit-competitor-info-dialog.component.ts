import { Component, OnInit, OnDestroy } from '@angular/core';
import { DynamicDialogConfig, DynamicDialogRef } from "primeng/dynamicdialog";
import { FormBuilder, FormGroup, Validators, AbstractControl } from '@angular/forms';
import { CompetitorInfo } from '../../../shared/competitor-info.service';
import { CountryService, Country } from '../../../shared/country.service';
import { Observable, Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';

@Component({
  selector: 'brevet-edit-competitor-info-dialog',
  templateUrl: './edit-competitor-info-dialog.component.html',
  styleUrls: ['./edit-competitor-info-dialog.component.scss']
})
export class EditCompetitorInfoDialogComponent implements OnInit, OnDestroy {

  competitorInfoForm: FormGroup;
  loading = false;
  countries: Country[] = [];
  private destroy$ = new Subject<void>();

  // TrackBy function for PrimeNG dropdown
  trackCountryById(index: number, country: Country): number {
    return country.country_id;
  }

  // Custom email validator - more permissive
  emailValidator(control: AbstractControl): {[key: string]: any} | null {
    if (!control.value) {
      return null; // Don't validate empty values, let required handle that
    }
    // Simple email validation - just check for @ and a dot after @
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const valid = emailRegex.test(control.value.trim());
    return valid ? null : { 'invalidEmail': true };
  }

  constructor(
    public ref: DynamicDialogRef,
    public config: DynamicDialogConfig,
    private formBuilder: FormBuilder,
    private countryService: CountryService
  ) {
    this.competitorInfoForm = this.formBuilder.group({
      email: ['', [this.emailValidator.bind(this)]],
      phone: [''],
      adress: [''],
      postal_code: [''],
      place: [''],
      country: [''],
      country_id: [null, [Validators.required]]
    });
  }

                      ngOnInit(): void {
    // Load countries first
    this.countryService.getAllCountries()
      .pipe(takeUntil(this.destroy$))
      .subscribe(countries => {
        this.countries = countries || [];

        // Initialize form after countries are loaded
        this.initializeForm();
      });

    // Set up country change listener
    this.setupCountryChangeListener();
  }

    private initializeForm(): void {
    if (this.config.data && this.config.data.competitorInfo) {
      const info = this.config.data.competitorInfo;

      // Set all form values except country_id first
      this.competitorInfoForm.patchValue({
        email: info.email || '',
        phone: info.phone || '',
        adress: info.adress || '',
        postal_code: info.postal_code || '',
        place: info.place || '',
        country: info.country || ''
      });

      // Handle country_id separately with proper PrimeNG timing
      if (info.country_id) {
        const countryId = Number(info.country_id);

        // Find the country to ensure it exists
        const foundCountry = this.countries.find(c => c.country_id === countryId);

        if (foundCountry) {
          // Use setTimeout to ensure PrimeNG dropdown is rendered
          setTimeout(() => {
            this.competitorInfoForm.get('country_id')?.setValue(countryId);

            // Also update the country name to be consistent
            this.competitorInfoForm.get('country')?.setValue(foundCountry.country_name_sv);
          }, 100);
        }
      }
    }
  }

  private setupCountryChangeListener(): void {
    this.competitorInfoForm.get('country_id')?.valueChanges
      .pipe(takeUntil(this.destroy$))
      .subscribe(countryId => {
        if (countryId && this.countries.length > 0) {
          const selectedCountry = this.countries.find(c => c.country_id === Number(countryId));
          if (selectedCountry) {
            this.competitorInfoForm.get('country')?.setValue(selectedCountry.country_name_sv, { emitEvent: false });
          }
        }
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  save() {
    if (this.competitorInfoForm.valid) {
      const competitorInfo: CompetitorInfo = this.competitorInfoForm.value;
      this.ref.close(competitorInfo);
    } else {
      // Mark all fields as touched to show validation errors
      this.competitorInfoForm.markAllAsTouched();
    }
  }

  cancel() {
    this.ref.close(null);
  }

  // Helper method to check if a field has errors
  isFieldInvalid(fieldName: string): boolean {
    const field = this.competitorInfoForm.get(fieldName);
    return !!(field && field.invalid && (field.dirty || field.touched));
  }

  // Helper method to get field error message
  getFieldError(fieldName: string): string {
    const field = this.competitorInfoForm.get(fieldName);
    if (field && field.errors) {
      if (field.errors['required']) {
        return `${fieldName} är obligatoriskt`;
      }
      if (field.errors['invalidEmail']) {
        return 'Ange en giltig email-adress';
      }
    }
    return '';
  }
}
