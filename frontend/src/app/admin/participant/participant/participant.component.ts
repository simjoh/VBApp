import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';
import {MenuItem} from 'primeng/api';
import {ParticipantComponentService} from "../participant-component.service";
import {PageHeaderConfig} from '../../../shared/components/page-header/page-header.component';
import {ActionCardConfig} from '../../../shared/components/action-card/action-card.component';

@Component({
  selector: 'brevet-participant',
  templateUrl: './participant.component.html',
  styleUrls: ['./participant.component.scss'],
  providers: [ParticipantComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class ParticipantComponent implements OnInit {

  designTabs = [];

  headerConfig: PageHeaderConfig = {
    icon: 'pi pi-users',
    title: 'Hantera Deltagare',
    description: 'Hantera deltagare, registreringar och resultat för evenemang'
  };

  actionCards: ActionCardConfig[] = [
    {
      icon: 'pi pi-list',
      title: 'Deltagarlista',
      description: 'Visa och hantera alla deltagare',
      action: 'list'
    },
    {
      icon: 'pi pi-upload',
      title: 'Ladda upp',
      description: 'Importera deltagare från fil',
      action: 'upload'
    }
  ];

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

    this.updateActionCardsActiveState();
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
