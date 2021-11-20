import {Component, OnInit, ChangeDetectionStrategy, ViewEncapsulation, ViewChild} from '@angular/core';
import {FormGroup, NgForm} from '@angular/forms';
import {DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import { User } from 'src/app/shared/api/api';
import {THIS_EXPR} from "@angular/compiler/src/output/output_ast";

@Component({
  selector: 'brevet-create-user-dialog',
  templateUrl: './create-user-dialog.component.html',
  styleUrls: ['./create-user-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class CreateUserDialogComponent implements OnInit {


  userForm: User;


  constructor(public ref: DynamicDialogRef, public config: DynamicDialogConfig) {
  }
  ngOnInit(): void {
    this.userForm = {
      user_uid: "",
      givenname: "",
      familyname: "",
      username: "",
      token: "",
      roles: []
    } as User;
  }

  addUser(contactForm: NgForm) {
    if (contactForm.valid){
      this.ref.close(this.getUserObject(contactForm));
    } else {
      contactForm.dirty
    }
  }

  cancel(){
    this.ref.close(null);
  }

  private getUserObject(form: NgForm): User {
    return this.userForm
  }
}
