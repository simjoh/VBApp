import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';
import {MenuItem} from "primeng/api";
import {TrackAdminComponentService} from "./track-admin-component.service";
import {CompactPageHeaderConfig, CompactActionCardConfig} from '../../shared/components';

@Component({
  selector: 'brevet-track-admin',
  templateUrl: './track-admin.component.html',
  styleUrls: ['./track-admin.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [TrackAdminComponentService]
})
export class TrackAdminComponent implements OnInit {

  designTabs = [];
  tabs = [];

  headerConfig: CompactPageHeaderConfig = {
    icon: 'pi pi-directions',
    title: 'Hantera Banor',
    description: 'Hantera banor, kontrollpunkter och evenemang'
  };

  actionCards: CompactActionCardConfig[] = [
    {
      icon: 'pi pi-list',
      title: 'Banlista',
      description: 'Visa och hantera alla banor',
      action: 'list'
    },
    {
      icon: 'pi pi-map',
      title: 'Banbyggare',
      description: 'Skapa och redigera banor',
      action: 'builder'
    }
  ];

  constructor(
    private router: Router,
    private tra: TrackAdminComponentService
  ) { }

  ngOnInit(): void {
    this.tra.init();

    this.tabs = [{
      id: 1,
      header: 'Tab 1'
    }, {
      id: 2,
      header: 'Tab 2'
    }];

    this.designTabs = [
      {
        label: "Banor",
        routerLink: 'brevet-track-list',
        icon: 'pi pi-list'
      },
      {
        label: "Banbyggare",
        routerLink: 'brevet-track-builder',
      }
    ] as MenuItem[];

    this.updateActionCardsActiveState();
  }

  isActive(tab: string): boolean {
    const currentUrl = this.router.url;
    switch (tab) {
      case 'list':
        return currentUrl.includes('brevet-track-list');
      case 'builder':
        return currentUrl.includes('brevet-track-builder');
      default:
        return false;
    }
  }

  navigateToTab(tab: string): void {
    switch (tab) {
      case 'list':
        this.router.navigate(['/admin/banor/brevet-track-list']);
        break;
      case 'builder':
        this.router.navigate(['/admin/banor/brevet-track-builder']);
        break;
    }
  }

  onActionCardClick(action: string): void {
    this.navigateToTab(action);
    this.updateActionCardsActiveState();
  }

  private updateActionCardsActiveState(): void {
    this.actionCards = this.actionCards.map(card => ({
      ...card,
      isActive: this.isActive(card.action || '')
    }));
  }
}
