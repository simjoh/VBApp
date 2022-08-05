import {Component, OnInit, ChangeDetectionStrategy, Input, OnChanges, SimpleChanges, Output, EventEmitter} from '@angular/core';
import {TracktableComponentService} from "./tracktable-component.service";
import {TrackRepresentation} from "../../api/api";
import { DialogService, DynamicDialogRef } from 'primeng/dynamicdialog';
import {BehaviorSubject} from "rxjs";
import {startWith} from "rxjs/operators";
import {LinkService} from "../../../core/link.service";
import {HttpMethod} from "../../../core/HttpMethod";


@Component({
  selector: 'brevet-track-table',
  templateUrl: './track-table.component.html',
  styleUrls: ['./track-table.component.scss'],
  providers: [TracktableComponentService],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class TrackTableComponent implements OnInit, OnChanges {

  $tracksviewinformation = this.tracktablecomponentService.tracks$;

  ref: DynamicDialogRef;

  @Output() reload = new EventEmitter();
  @Input() expandable: boolean;
  @Input() editable: boolean;
  @Input() readonly : boolean;
  @Input() tracks: TrackRepresentation[];

  constructor(private tracktablecomponentService: TracktableComponentService,public dialogService: DialogService, private link: LinkService) { }

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

    isPossibleToPublishResults(trackRepresentation: TrackRepresentation): boolean{
        return !this.link.exists(trackRepresentation.links, 'relation.track.publisresults', HttpMethod.PUT);
  }

 async publish(trackRepresentation: TrackRepresentation) {
    trackRepresentation.active = !trackRepresentation.active;
    await this.tracktablecomponentService.publishReultLinkExists(trackRepresentation)
    await this.tracktablecomponentService.publishResults(trackRepresentation).then((res) => {
     this.reload.emit(trackRepresentation.track_uid);
   })
  }
}
