import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'closeOrOpen',
    standalone: false
})
export class CloseOrOpenPipe implements PipeTransform {

  transform(value: unknown, ...args: unknown[]): unknown {
    return null;
  }

}
