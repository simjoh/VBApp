import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { NgForm } from '@angular/forms';
import { DynamicDialogConfig, DynamicDialogRef } from "primeng/dynamicdialog";
import { ClubRepresentation } from "../../../shared/api/api";

@Component({
  selector: 'brevet-edit-club-dialog',
  templateUrl: './edit-club-dialog.component.html',
  styleUrls: ['./edit-club-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EditClubDialogComponent implements OnInit {

  formData: ClubForm;
  originalClub: ClubRepresentation;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) { }

  ngOnInit(): void {
    this.originalClub = this.config.data.club;
    const title = this.originalClub.title;
    const acpCode = this.originalClub.acp_code;

    this.formData = {
      title: title,
      acp_code: acpCode && acpCode !== "0" ? acpCode : ""
    };
  }

  onSubmit(clubForm: NgForm) {
    if (clubForm.invalid) {
      return;
    }

    const title = clubForm.controls.title.value;
    const acpCode = clubForm.controls.acp_code.value;

    const club: ClubRepresentation = {
      club_uid: this.originalClub.club_uid,
      title: title,
      acp_code: acpCode && acpCode.trim() !== "" ? acpCode : null,
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
  acp_code: string;
}
