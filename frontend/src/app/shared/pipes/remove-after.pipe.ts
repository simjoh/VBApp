import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'removeAfter'
})
export class RemoveAfterPipe implements PipeTransform {

  transform(value: string, ...args: unknown[]): unknown {

    return value.substr(0, value.lastIndexOf(":"));
  }

}
