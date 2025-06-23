import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import { MessageService } from 'primeng/api';
import { OrganizerRepresentation } from '../organizer.service';

@Component({
  selector: 'brevet-create-organizer-dialog',
  templateUrl: './create-organizer-dialog.component.html',
  styleUrls: ['./create-organizer-dialog.component.scss']
})
export class CreateOrganizerDialogComponent implements OnInit {
  organizerForm: FormGroup;
  loading = false;

  constructor(
    private fb: FormBuilder,
    private dialogRef: DynamicDialogRef,
    private config: DynamicDialogConfig,
    private messageService: MessageService
  ) {
    this.organizerForm = this.fb.group({
      organization_name: ['', [Validators.required, Validators.minLength(2)]],
      contact_person_name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      website: [''],
      website_pay: [''],
      logo_svg: [''],
      description: [''],
      active: [true]
    });
  }

  ngOnInit(): void {
    // Initialize form if needed
  }

  onSubmit(): void {
    if (this.organizerForm.valid) {
      this.loading = true;
      const organizer: OrganizerRepresentation = this.organizerForm.value;

      // Close dialog with the organizer data
      this.dialogRef.close(organizer);
    } else {
      this.messageService.add({
        severity: 'error',
        summary: 'Validation Error',
        detail: 'Please fill in all required fields correctly.'
      });
    }
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  getErrorMessage(controlName: string): string {
    const field = this.organizerForm.get(controlName);
    if (field?.errors) {
      if (field.errors['required']) {
        return `${controlName.replace('_', ' ')} is required`;
      }
      if (field.errors['minlength']) {
        return `${controlName.replace('_', ' ')} must be at least ${field.errors['minlength'].requiredLength} characters`;
      }
      if (field.errors['email']) {
        return 'Please enter a valid email address';
      }
    }
    return '';
  }
}
