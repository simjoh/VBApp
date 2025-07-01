import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';
import {MenuItem} from 'primeng/api';
import {ParticipantComponentService} from "../participant-component.service";

@Component({
  selector: 'brevet-participant',
  templateUrl: './participant.component.html',
  styleUrls: ['./participant.component.scss'],
  providers: [ParticipantComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class ParticipantComponent implements OnInit {

  designTabs = [];

  constructor(
    private router: Router,
    private participantService: ParticipantComponentService
  ) { }

  ngOnInit(): void {
    this.designTabs = [
      {
        label: "Lista",
        routerLink: 'brevet-participant-list',
        icon: 'pi pi-list'
      }
    ] as MenuItem[];
  }

  navigateToTab(tab: string): void {
    switch (tab) {
      case 'list':
        this.router.navigate(['/admin/participant/brevet-participant-list']);
        break;
      case 'upload':
        this.router.navigate(['/admin/participant/brevet-participant-upload']);
        break;
    }
  }

  isActive(tab: string): boolean {
    const currentUrl = this.router.url;
    switch (tab) {
      case 'list':
        return currentUrl.includes('brevet-participant-list');
      case 'upload':
        return currentUrl.includes('brevet-participant-upload');
      default:
        return false;
    }
  }
}
