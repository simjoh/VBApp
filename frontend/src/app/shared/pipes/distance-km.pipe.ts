import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'distanceKm'
})
export class DistanceKmPipe implements PipeTransform {
  transform(value: string, ...args: unknown[]): unknown {
    return  value.substring(0, value.indexOf('.'))  + ' km';
  }
}
