import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { DynamicDialogConfig, DynamicDialogRef } from "primeng/dynamicdialog";
import { ClubRepresentation } from "../../../shared/api/api";

@Component({
  selector: 'brevet-edit-club-dialog',
  templateUrl: './edit-club-dialog.component.html',
  styleUrls: ['./edit-club-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EditClubDialogComponent implements OnInit {
  originalClub: ClubRepresentation;
  clubForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    public ref: DynamicDialogRef,
    public config: DynamicDialogConfig
  ) {
    this.clubForm = this.formBuilder.group({
      title: ['', Validators.required],
      acp_kod: ['']
    });
  }

  ngOnInit(): void {
    this.originalClub = this.config.data.club;

    this.clubForm.patchValue({
      title: this.originalClub.title,
      acp_kod: this.originalClub.acp_kod || ''
    });
  }

  onSubmit() {
    if (this.clubForm.invalid) {
      return;
    }

    const formValue = this.clubForm.value;

    // Handle ACP kod properly - preserve value if provided, set to null only if explicitly cleared
    let acpKod = null;
    if (formValue.acp_kod !== undefined && formValue.acp_kod !== null) {
      const trimmedValue = formValue.acp_kod.trim();
      acpKod = trimmedValue !== "" ? trimmedValue : null;
    }

    const club: ClubRepresentation = {
      club_uid: this.originalClub.club_uid,
      title: formValue.title,
      acp_kod: acpKod,
      links: this.originalClub.links
    };

    this.ref.close(club);
  }

  cancel() {
    this.ref.close(null);
  }
}

export interface ClubForm {
  title: string;
  acp_kod: string;
}
