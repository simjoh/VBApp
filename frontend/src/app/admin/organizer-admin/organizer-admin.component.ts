import {ChangeDetectionStrategy, Component, OnInit} from '@angular/core';
import {OrganizerService} from "./organizer.service";

@Component({
  selector: 'brevet-organizer-admin',
  standalone: true,
  imports: [],
  templateUrl: './organizer-admin.component.html',
  styleUrl: './organizer-admin.component.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class OrganizerAdminComponent implements OnInit{



  constructor(private organizerservice: OrganizerService) {
  }

  ngOnInit(): void {

    this.organizerservice.getAllOrganizers();
  }

}
