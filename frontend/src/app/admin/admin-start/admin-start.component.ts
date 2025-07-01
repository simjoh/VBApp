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

  constructor(private router: Router, private participantService: ParticipantService) {
    this.participantStats$ = this.participantService.getParticipantStats();
  }

  ngOnInit(): void {
  }

  navigate(path: string): void {
    this.router.navigate([path]);
  }
}
