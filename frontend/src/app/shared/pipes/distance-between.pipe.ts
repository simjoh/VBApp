import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'distanceBetween'
})
export class DistanceBetweenPipe implements PipeTransform {

  transform(value: any, dist: string): unknown {
    return  Number(value) - Number(dist)  ;
  }

}
