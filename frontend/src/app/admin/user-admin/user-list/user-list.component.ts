import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {UserService} from "../user.service";
import {User} from "../../../shared/api/api";
import {Observable} from "rxjs";
import { Table } from 'primeng/table';
import {DialogService} from 'primeng/dynamicdialog';
import {ConfirmationService, OverlayService, PrimeNGConfig} from 'primeng/api';
import {CreateUserDialogComponent} from "../create-user-dialog/create-user-dialog.component";
import {EditUserDialogComponent} from "../edit-user-dialog/edit-user-dialog.component";
import {DeviceDetectorService} from "ngx-device-detector";
import {map, tap} from "rxjs/operators";

@Component({
  selector: 'brevet-user-list',
  templateUrl: './user-list.component.html',
  styleUrls: ['./user-list.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers:[DialogService, ConfirmationService]
})
export class UserListComponent implements OnInit {

  @ViewChild('dt',{ static: false }) table: Table;
  $users = this.userService.usersWithAdd$.pipe(
    tap(users => console.log("Users in list component:", users)),
    map((s:Array<User>) => {
      return s;
    })
  ) as Observable<User[]>;
  selectedCustomers: User[];

  loading: boolean = false;

  constructor(private userService: UserService,
              private primengConfig: PrimeNGConfig,
              private dialogService: DialogService,
              private confirmationService: ConfirmationService,
              private deviceDetector: DeviceDetectorService) { }

  ngOnInit(): void {
    this.primengConfig.ripple = true;
  }


        editProduct(user: User) {
    console.log("Edit product - User from list:", user);

    let width;
    if ( this.deviceDetector.isDesktop()){
      width = "30%";
    } else {
      width = "80%"
    }

    // Fetch fresh user data from backend before opening dialog
    console.log("Fetching fresh user data for:", user.user_uid);
    this.userService.getUser(user.user_uid).subscribe((freshUser: User) => {
      console.log("Fresh user data from backend:", freshUser);
      console.log("Fresh user roles:", freshUser.roles);

      const ref = this.dialogService.open(EditUserDialogComponent, {
        data: {
          user: freshUser
        },
        header: 'Redigera Användare',
        width: width
      });

      ref.onClose.subscribe((updatedUser: User) => {
        if (updatedUser) {
          console.log("Updating user:", updatedUser);
          this.userService.updateUserInList(updatedUser);
        }
      });
    });
  }

  deleteProduct(product: any) {
    this.confirmationService.confirm({
      message: 'Är du säker på att du vill ta bort ' + product + '?',
      header: 'Bekräfta',
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

    let width;
    if ( this.deviceDetector.isDesktop()){
      width = "30%";
    } else {
      width = "80%"
    }

    const ref = this.dialogService.open(CreateUserDialogComponent, {
      data: {
        id: '51gF3'
      },
      header: 'Lägg till Användare',
      width: width
    });

    ref.onClose.subscribe((user: User) => {
      if (user) {
        console.log(user);
        this.userService.newUser(user);
      }
    });




  }
}
