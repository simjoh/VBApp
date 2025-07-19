import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import { ParticipantService, ParticipantStats } from '../../shared/participant.service';

@Component({
  selector: 'app-admin-start',
  templateUrl: './admin-start.component.html',
  styleUrls: ['./admin-start.component.scss']
})
export class AdminStartComponent implements OnInit {
  participantStats$: Observable<ParticipantStats>;
  isSuperUser = false;

  constructor(private router: Router, private participantService: ParticipantService) {
    this.participantStats$ = this.participantService.getParticipantStats();
    this.checkUserRoles();
  }

  ngOnInit(): void {
  }

  private checkUserRoles(): void {
    const activeUser = JSON.parse(localStorage.getItem('activeUser') || '{}');
    this.isSuperUser = activeUser.roles?.includes('SUPERUSER') || false;
  }

  navigate(path: string): void {
    this.router.navigate([path]);
  }
}
