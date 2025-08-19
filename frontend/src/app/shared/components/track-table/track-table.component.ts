import {Component, OnInit, ChangeDetectionStrategy, Input, OnChanges, SimpleChanges, Output, EventEmitter, ChangeDetectorRef} from '@angular/core';
import {TracktableComponentService} from "./tracktable-component.service";
import {TrackRepresentation} from "../../api/api";
import { DialogService, DynamicDialogRef } from 'primeng/dynamicdialog';
import {BehaviorSubject} from "rxjs";
import {startWith} from "rxjs/operators";
import {LinkService} from "../../../core/link.service";
import {HttpMethod} from "../../../core/HttpMethod";
import { faTowerBroadcast, faRotateLeft } from '@fortawesome/free-solid-svg-icons';

@Component({
  selector: 'brevet-track-table',
  templateUrl: './track-table.component.html',
  styleUrls: ['./track-table.component.scss'],
  providers: [TracktableComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackTableComponent implements OnInit, OnChanges {
  faBroadcast = faTowerBroadcast;
  faUndo = faRotateLeft;

  $tracksviewinformation = this.tracktablecomponentService.tracks$;

  ref: DynamicDialogRef;

  @Output() reload = new EventEmitter();
  @Input() expandable: boolean;
  @Input() editable: boolean;
  @Input() readonly : boolean;
  @Input() tracks: TrackRepresentation[];

  constructor(
    private tracktablecomponentService: TracktableComponentService,
    public dialogService: DialogService,
    private link: LinkService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
  //  this.tracktablecomponentService.initiateTracks(this.tracks);
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes && changes.tracks) {
      this.tracks = changes.tracks.currentValue;
      this.tracktablecomponentService.initiateTracks(changes.tracks.currentValue);
    }
  }


  isPossibleToDelete(track: TrackRepresentation) {
    return this.tracktablecomponentService.linkExists(track);
  }

  async remove(trackRepresentation: TrackRepresentation) {
   await this.tracktablecomponentService.remove(trackRepresentation).then(() => {
      this.reload.emit(true);
    });
  }

  showPreview() {
    // this.ref = this.dialogService.open(ProductListDemo, {
    //   header: 'Choose a Product',
    //   width: '70%',
    //   contentStyle: {"max-height": "500px", "overflow": "auto"},
    //   baseZIndex: 10000
    // });
    //
    // this.ref.onClose.subscribe((product: Product) =>{
    //   if (product) {
    //     this.messageService.add({severity:'info', summary: 'Product Selected', detail: product.name});
    //   }
    // });
  }

  /**
   * Returns true if the track is currently published (active=true)
   * This means the "unpublish" button should be shown
   */
  isTrackPublished(trackRepresentation: TrackRepresentation): boolean {
    return this.link.exists(trackRepresentation.links, 'relation.track.undopublisresults', HttpMethod.PUT);
  }

  /**
   * Returns true if the track is currently unpublished (active=false)
   * This means the "publish" button should be shown
   */
  isTrackUnpublished(trackRepresentation: TrackRepresentation): boolean {
    return this.link.exists(trackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT);
  }

  async publish(trackRepresentation: TrackRepresentation) {
    try {
      // Disable buttons during the operation to prevent double-clicks
      const publishButton = document.querySelector(`[data-track-uid="${trackRepresentation.track_uid}"] .publish-btn`) as HTMLButtonElement;
      const unpublishButton = document.querySelector(`[data-track-uid="${trackRepresentation.track_uid}"] .unpublish-btn`) as HTMLButtonElement;

      if (publishButton) publishButton.disabled = true;
      if (unpublishButton) unpublishButton.disabled = true;

      await this.tracktablecomponentService.publishResults(trackRepresentation);

      // Add a minimal delay to ensure the backend transaction is committed
      setTimeout(() => {
        this.reload.emit(trackRepresentation.track_uid);
        this.cdr.detectChanges(); // Force change detection
      }, 100);

    } catch (error) {
      console.error('Error publishing/unpublishing track:', error);
      // Still reload to refresh the state even on error
      this.reload.emit(trackRepresentation.track_uid);
    } finally {
      // Re-enable buttons after operation
      setTimeout(() => {
        const publishButton = document.querySelector(`[data-track-uid="${trackRepresentation.track_uid}"] .publish-btn`) as HTMLButtonElement;
        const unpublishButton = document.querySelector(`[data-track-uid="${trackRepresentation.track_uid}"] .unpublish-btn`) as HTMLButtonElement;

        if (publishButton) publishButton.disabled = false;
        if (unpublishButton) unpublishButton.disabled = false;
      }, 200);
    }
  }
}
