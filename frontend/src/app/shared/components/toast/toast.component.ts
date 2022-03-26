import { Component, OnInit, ChangeDetectionStrategy } from '@angular/core';
import { MessageService, PrimeNGConfig } from 'primeng/api';

@Component({
  selector: 'brevet-toast',
  templateUrl: './toast.component.html',
  styleUrls: ['./toast.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ToastComponent implements OnInit {

  constructor(private messageService: MessageService,private primengConfig: PrimeNGConfig) { }

  ngOnInit(): void {

  }



  showSuccess() {
    this.messageService.add({key: 'tc', severity:'warn', summary: 'Warn', detail: 'Message Content 2'});
  }
}
