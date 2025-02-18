import { ChangeDetectionStrategy, Component } from '@angular/core';
import {TrackModule} from "../../track/track.module";
import {SvgDisplayComponent} from "../../../shared/components/svg-display/svg-display.component";

@Component({
    selector: 'brevet-acp-report',
    imports: [
        TrackModule
    ],
    templateUrl: './acp-report.component.html',
    styleUrl: './acp-report.component.scss',
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class AcpReportComponent {

}
