import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { NgForm, FormBuilder, Validators } from '@angular/forms';
import { DynamicDialogConfig, DynamicDialogRef } from "primeng/dynamicdialog";
import { ClubRepresentation } from "../../../shared/api/api";

@Component({
  selector: 'brevet-create-club-dialog',
  templateUrl: './create-club-dialog.component.html',
  styleUrls: ['./create-club-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateClubDialogComponent implements OnInit {

  clubForm = this.formBuilder.group({
    title: ['', Validators.required],
    acp_kod: ['']
  });

  constructor(
    private formBuilder: FormBuilder,
    public ref: DynamicDialogRef,
    public config: DynamicDialogConfig
  ) { }

  ngOnInit() {
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
      club_uid: "",
      title: formValue.title,
      acp_kod: acpKod,
      links: []
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
