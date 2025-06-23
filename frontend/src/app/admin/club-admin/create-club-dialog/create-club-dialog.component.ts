import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { NgForm } from '@angular/forms';
import { DynamicDialogConfig, DynamicDialogRef } from "primeng/dynamicdialog";
import { ClubRepresentation } from "../../../shared/api/api";

@Component({
  selector: 'brevet-create-club-dialog',
  templateUrl: './create-club-dialog.component.html',
  styleUrls: ['./create-club-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateClubDialogComponent implements OnInit {

  clubForm: ClubFormModel;

  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) { }

  ngOnInit(): void {
    this.clubForm = this.createObject();
  }

  private createObject(): ClubFormModel {
    return {
      club_uid: "",
      title: "",
      acp_code: ""
    } as ClubFormModel;
  }

  addClub(clubForm: NgForm) {
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
      club_uid: "",
      title: clubForm.controls.title.value,
      acp_code: acpCode && acpCode.trim() !== "" ? acpCode : null,
      links: []
    } as ClubRepresentation;
  }
}

export class ClubFormModel {
  club_uid: string;
  title: string;
  acp_code: string;
}
