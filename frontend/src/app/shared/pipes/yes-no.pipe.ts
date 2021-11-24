import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'yesNo'
})
export class YesNoPipe implements PipeTransform {

  transform(value: unknown, ...args: unknown[]): unknown {
    if (typeof (value) !== "undefined" && value !== null) {
      if (value.toString().toLowerCase() === "true") {
        return "Ja"
      } else if (value.toString().toLowerCase() === "false") {
        return "Nej"
      }
    }
    return value;
  }

}
