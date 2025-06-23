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

  clubForm: ClubFormModel;
  originalClub: ClubRepresentation;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) { }

  ngOnInit(): void {
    this.originalClub = this.config.data.club;
    this.clubForm = this.createObject();
  }

  private createObject(): ClubFormModel {
    const acpCode = this.originalClub.acp_code;
    return {
      club_uid: this.originalClub.club_uid,
      title: this.originalClub.title,
      acp_code: acpCode && acpCode !== "0" ? acpCode : ""
    } as ClubFormModel;
  }

  updateClub(clubForm: NgForm) {
    if (clubForm.valid) {
      this.ref.close(this.getClubObject(clubForm));
    } else {
      clubForm.dirty;
    }
  }

  cancel() {
    this.ref.close(null);
  }

  private getClubObject(clubForm: NgForm) {
    const acpCode = clubForm.controls.acp_code.value;
    return {
      club_uid: this.originalClub.club_uid,
      title: clubForm.controls.title.value,
      acp_code: acpCode && acpCode.trim() !== "" ? acpCode : null,
      links: this.originalClub.links
    } as ClubRepresentation;
  }
}

export class ClubFormModel {
  club_uid: string;
  title: string;
  acp_code: string;
}
