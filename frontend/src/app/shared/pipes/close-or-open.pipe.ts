import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'closeOrOpen'
})
export class CloseOrOpenPipe implements PipeTransform {

  transform(value: unknown, ...args: unknown[]): unknown {
    return null;
  }

}
