import { Component, OnInit } from '@angular/core';


@Component({
  selector: 'brevet-competitors-list',
  templateUrl: './competitors-list.component.html',
  styleUrls: ['./competitors-list.component.scss']
})
export class CompetitorsListComponent implements OnInit {
  competitors = [{  
    id: 1,  
    competitor: 'Simon'  
}, {  
    id: 2,  
    competitor: 'Bengt'  
}, ];  
  constructor() { }

  onSelect() {

  }
  ngOnInit(): void {
  }

}
