import {ChangeDetectionStrategy, Component, Input} from '@angular/core';
import {EventRepresentation, OrganizerRepresentation} from "../../../shared/api/api";
import {SharedModule} from "../../../shared/shared.module";

@Component({
    selector: 'brevet-organizer-info-popover',
    imports: [
        SharedModule
    ],
    templateUrl: './organizer-info-popover.component.html',
    styleUrl: './organizer-info-popover.component.scss',
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class OrganizerInfoPopoverComponent {


  @Input() event: OrganizerRepresentation;

}
