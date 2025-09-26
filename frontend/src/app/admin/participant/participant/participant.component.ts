import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {MenuItem} from 'primeng/api';
import {ParticipantComponentService} from "../participant-component.service";
import {TrackService} from '../../../shared/track-service';
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
    title: 'participant.manageParticipants',
    description: 'participant.manageParticipantsDescription'
  };

  actionCards: ActionCardConfig[] = [
    {
      icon: 'pi pi-list',
      title: 'participant.participantList',
      description: 'participant.participantListDescription',
      action: 'list'
    },
    {
      icon: 'pi pi-upload',
      title: 'participant.upload',
      description: 'participant.uploadDescription',
      action: 'upload'
    }
  ];

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private participantService: ParticipantComponentService,
    private trackService: TrackService
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

    // Check for track query parameter
    this.route.queryParams.subscribe(params => {
      console.log('Participant component received query params:', params);
      if (params['track']) {
        console.log('Setting track in track service:', params['track']);
        // Set the track in the track service (this is what the participant service listens to)
        this.trackService.currentTrack(params['track']);
        // Navigate to the list view to show the filtered participants
        this.router.navigate(['/admin/participant/brevet-participant-list'], {
          queryParams: { track: params['track'] },
          replaceUrl: true
        });
      }
    });
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
