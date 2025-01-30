import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {ActivatedRoute} from "@angular/router";

@Component({
  selector: 'brevet-participants-to-report-table',
  standalone: false,
  templateUrl: './participants-to-report-table.component.html',
  styleUrl: './participants-to-report-table.component.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ParticipantsToReportTableComponent implements OnInit {
  code: string | null = null;

  constructor(private route: ActivatedRoute){}


  ngOnInit() {
    this.code = this.route.snapshot.paramMap.get('code');
    console.log('Code from route:', this.code);
  }

  exportCSV($event: MouseEvent) {

  }


}
