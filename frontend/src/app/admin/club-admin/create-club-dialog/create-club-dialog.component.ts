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

  clubForm = {
    title: "",
    acp_code: ""
  };

  constructor(
    public ref: DynamicDialogRef,
    public config: DynamicDialogConfig
  ) { }

  ngOnInit() {
  }

  onSubmit(clubForm: NgForm) {
    if (clubForm.invalid) {
      return;
    }

    const title = clubForm.controls.title.value;
    const acpCode = clubForm.controls.acp_code.value;

    const club: ClubRepresentation = {
      club_uid: "",
      title: title,
      acp_code: acpCode && acpCode.trim() !== "" ? acpCode : null,
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
  acp_code: string;
}
