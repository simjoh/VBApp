import { Component, OnInit, ViewChild, ChangeDetectorRef } from '@angular/core';
import { UserAdminComponentService } from "./user-admin-component.service";
import { ConfirmationService, MessageService } from "primeng/api";
import { Table } from 'primeng/table';
import { Observable } from 'rxjs';
import { User } from '../../shared/api/api';
import { DialogService, DynamicDialogRef } from 'primeng/dynamicdialog';
import { EditUserDialogComponent } from './edit-user-dialog/edit-user-dialog.component';
import { CreateUserDialogComponent } from './create-user-dialog/create-user-dialog.component';

interface AdminUser extends User {
  name: string;
  email: string;
  permissions: string;
  active: boolean;
}

@Component({
  selector: 'brevet-user-admin',
  templateUrl: './user-admin.component.html',
  styleUrls: ['./user-admin.component.scss'],
  providers: [UserAdminComponentService, ConfirmationService, MessageService, DialogService]
})
export class UserAdminComponent implements OnInit {
  @ViewChild('dt') table!: Table;

  users$: Observable<AdminUser[]>;
  ref: DynamicDialogRef | undefined;

  constructor(
    private userService: UserAdminComponentService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private dialogService: DialogService,
    private cdr: ChangeDetectorRef
  ) {
    this.users$ = this.userService.getAllUsers();
  }

  ngOnInit(): void {}

  openNew() {
    this.ref = this.dialogService.open(CreateUserDialogComponent, {
      header: 'Ny användare',
      width: '50%',
      data: {}
    });
    this.ref.onClose.subscribe((newUser) => {
      if (newUser) {
        this.userService.createUser(newUser).subscribe({
          next: () => {
            this.users$ = this.userService.getAllUsers();
            this.cdr.markForCheck();
          },
          error: () => this.messageService.add({
            severity: 'error',
            summary: 'Fel',
            detail: 'Kunde inte skapa användaren',
            life: 3000
          })
        });
      }
    });
  }

  editUser(user: AdminUser) {
    this.ref = this.dialogService.open(EditUserDialogComponent, {
      header: 'Redigera användare',
      width: '50%',
      data: { user }
    });
    this.ref.onClose.subscribe((updatedUser) => {
      if (updatedUser) {
        this.userService.updateUser(updatedUser.user_uid, updatedUser).subscribe({
          next: () => {
            this.users$ = this.userService.getAllUsers();
            this.cdr.markForCheck();
          },
          error: () => this.messageService.add({
            severity: 'error',
            summary: 'Fel',
            detail: 'Kunde inte uppdatera användaren',
            life: 3000
          })
        });
      }
    });
  }

  deleteUser(userId: string) {
    this.confirmationService.confirm({
      message: 'Är du säker på att du vill ta bort denna användare?',
      header: 'Bekräfta borttagning',
      icon: 'pi pi-exclamation-triangle',
      accept: () => {
        this.userService.deleteUser(userId).subscribe({
          next: () => {
            this.users$ = this.userService.getAllUsers();
            this.messageService.add({
              severity: 'success',
              summary: 'Framgång',
              detail: 'Användaren har tagits bort',
              life: 3000
            });
          },
          error: (error) => {
            this.messageService.add({
              severity: 'error',
              summary: 'Fel',
              detail: 'Kunde inte ta bort användaren',
              life: 3000
            });
          }
        });
      }
    });
  }

  canDelete(user: AdminUser): boolean {
    return user.permissions !== 'Admin';
  }
}
