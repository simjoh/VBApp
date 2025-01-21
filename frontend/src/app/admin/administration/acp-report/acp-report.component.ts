import { ChangeDetectionStrategy, Component } from '@angular/core';
import {TrackModule} from "../../track/track.module";

@Component({
  selector: 'brevet-acp-report',
  standalone: true,
  imports: [
    TrackModule
  ],
  templateUrl: './acp-report.component.html',
  styleUrl: './acp-report.component.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class AcpReportComponent {

}
