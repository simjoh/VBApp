import { Component, PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { Kontroll } from './kontroll';
// interface Kontroll {
//   name: string;
//   //flag: string;
//   latitud: number;
//   longitud: number;
// }
const KONTROLLER: Kontroll[] = [
  {
    name: 'Brännland',
    id:0,
    //flag: 'f/f3/Flag_of_Russia.svg',
    latitud: 17075200,
    longitud: 146989754
  },
  {
    name: 'Åbrånet',
    id:1,
    //flag: 'c/cf/Flag_of_Canada.svg',
    latitud: 9976140,
    longitud: 36624199
  },
  {
    name: 'Bengans mekansiska',
    id:2,
    //flag: 'a/a4/Flag_of_the_United_States.svg',
    latitud: 9629091,
    longitud: 324459463
  },
  {
    name: 'OK Sorsele',
    id:3,
    //flag: 'f/fa/Flag_of_the_People%27s_Republic_of_China.svg',
    latitud: 9596960,
    longitud: 1409517397
  }
];

function search(text: string, pipe: PipeTransform): Kontroll[] {
  return KONTROLLER.filter(kontroll => {
    const term = text.toLowerCase();
    return kontroll.name.toLowerCase().includes(term)
        || pipe.transform(kontroll.latitud).includes(term)
        || pipe.transform(kontroll.longitud).includes(term);
  });
}

@Component({
  selector: 'brevet-kontroller',
  templateUrl: './kontroller.component.html',
  styleUrls: ['./kontroller.component.scss'],
  providers: [DecimalPipe]
})
export class KontrollerComponent  {

  kontroller$: Observable<Kontroll[]>;
  filter = new FormControl('');

  constructor(pipe: DecimalPipe) {
    this.kontroller$ = this.filter.valueChanges.pipe(
      startWith(''),
      map(text => search(text, pipe))
    );
  }
}
