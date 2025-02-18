import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'distanceBetween',
    standalone: false
})
export class DistanceBetweenPipe implements PipeTransform {

  transform(value: any, dist: string, reverse:boolean): unknown {


    if (!reverse){
      return  Number(value) - Number(dist)  ;
    } else {
       return  Number(dist) - Number(value)
    }

  }

}
