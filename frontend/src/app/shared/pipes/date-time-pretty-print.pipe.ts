import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'dateTimePrettyPrint'
})
export class DateTimePrettyPrintPipe implements PipeTransform {

  transform(value: string, ...args: unknown[]): string {

    if (value === undefined || value === ""){
      return "";
    }

    var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const month = ['Januray', 'February', 'March', 'April', 'May', 'June', 'Juli', 'August', 'September', 'October', 'November', 'December'];
    var parseddate = Date.parse(value.replace(/ /g,"T"))
    const d = new Date(parseddate);
    const dayName = days[d.getDay()];
    const monthName = month[d.getMonth()];
    const dayDigit = d.getDate();
    const h = this.addZero(d.getHours());
    const m = this.addZero(d.getMinutes());

    return  dayName + ' ' + monthName +  ' ' + dayDigit + ' ' +  h + ":" + m

    // return  String(value);
  }

  private addZero(i) {
    if (i < 10) {
      i = "0" + i;
    }
    return i;
  }


}
