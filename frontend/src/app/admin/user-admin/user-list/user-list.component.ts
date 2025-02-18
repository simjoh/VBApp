import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {UserService} from "../user.service";
import {User} from "../../../shared/api/api";
import {Observable} from "rxjs";
import { Table } from 'primeng/table';
import {DialogService} from 'primeng/dynamicdialog';
import {ConfirmationService} from 'primeng/api';
import {CreateUserDialogComponent} from "../create-user-dialog/create-user-dialog.component";
import {DeviceDetectorService} from "ngx-device-detector";
import {map, tap} from "rxjs/operators";
import {AuthService} from "../../../core/auth/auth.service";
import {Roles} from "../../../shared/roles";
import {defaultDialogConfig} from "../../../shared/utils/dialog-config";

@Component({
    selector: 'brevet-user-list',
    templateUrl: './user-list.component.html',
    styleUrls: ['./user-list.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    providers: [DialogService, ConfirmationService],
    standalone: false
})
export class UserListComponent implements OnInit {

  @ViewChild('dt',{ static: false }) table: Table;
  $users = this.userService.usersWithAdd$.pipe(
    map((s:Array<User>) => {
      return s;
    })
  ) as Observable<User[]>;
  selectedCustomers: User[];

  loading: boolean = false;

  roles: any[] = []

  $activetabs = this.authService.$auth$.pipe(
    tap(aciveuser => {
      this.roles = aciveuser.roles;
    })
  ).subscribe();

  constructor(private userService: UserService,
              private dialogService: DialogService,
              private confirmationService: ConfirmationService,
              private deviceDetector: DeviceDetectorService, private authService: AuthService) { }

  ngOnInit(): void {
  }


  editProduct(product: any) {
    console.log(product);
  }

  deleteProduct(product: any) {
    this.confirmationService.confirm({
      message: 'Are you sure you want to delete ' + product + '?',
      header: 'Confirm',
      icon: 'pi pi-exclamation-triangle',
      accept: () => {
        console.log(product)
        this.userService.deleterUser(product);
      },
      reject: () => {
        console.log("reject");
    }
    });
  }

  openNew() {
    this.dialogService.open(CreateUserDialogComponent, {
      ...defaultDialogConfig,
      header: 'Skapa användare',
      width: '40rem',
      closeOnEscape: true,
      showHeader: true,
      closable: true,

      contentStyle: { 'max-height': '650px', 'overflow': 'auto' }
    });
  }
}
