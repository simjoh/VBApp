import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'datetimeBetween'
})
export class DatetimeBetweenPipe implements PipeTransform {

  transform(datestart: any, dateend: string,): unknown {

    let currentDate = new Date();
    const dateOne = new Date(datestart);
    const dateTwo = new Date(dateend);
    if (currentDate >= dateOne && currentDate <= dateTwo){
      return true
    }

    // if (currentDate > dateOne){
    //   return true
    // }

    return false;
  }

}
