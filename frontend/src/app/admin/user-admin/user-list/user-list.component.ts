import {Component, OnInit, ChangeDetectionStrategy, ViewChild} from '@angular/core';
import {UserService} from "../user.service";
import {User} from "../../../shared/api/api";
import {Observable} from "rxjs";
import { Table } from 'primeng/table';
import {DialogService} from 'primeng/dynamicdialog';
import {ConfirmationService, OverlayService, PrimeNGConfig} from 'primeng/api';
import {CreateUserDialogComponent} from "../create-user-dialog/create-user-dialog.component";
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
    map((s:Array<User>) => {
      // this.table. = 0;
      return s;
    })
  ) as Observable<User[]>;
  selectedCustomers: User[];

  loading: boolean = false;

  // activityValues: number[] = [0, 100];

  // cols: any[];


  constructor(private userService: UserService,
              private primengConfig: PrimeNGConfig,
              private dialogService: DialogService,
              private confirmationService: ConfirmationService,
              private deviceDetector: DeviceDetectorService) { }

  ngOnInit(): void {
    this.primengConfig.ripple = true;
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
      header: 'Lägg till användare',
    });

    ref.onClose.subscribe((user: User) => {
      if (user) {
        console.log(user);
        this.userService.newUser(user);
      }
    });




  }
  viewUser(user_uid: any) {

  }
}
