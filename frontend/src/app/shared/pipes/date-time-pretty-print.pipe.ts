import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'dateTimePrettyPrint'
})
export class DateTimePrettyPrintPipe implements PipeTransform {

  transform(value: string, ...args: unknown[]): string {
    if (value === undefined || value === ""){
      return "";
    }

    try {
      var parseddate = Date.parse(value.replace(/ /g,"T"));
      const d = new Date(parseddate);

      const year = d.getFullYear();
      const month = this.addZero(d.getMonth() + 1); // Month is 0-indexed
      const day = this.addZero(d.getDate());
      const hours = this.addZero(d.getHours());
      const minutes = this.addZero(d.getMinutes());

      return `${year}-${month}-${day} ${hours}:${minutes}`;
    } catch (e) {
      console.error('Error formatting date:', e);
      return "";
    }
  }

  // Static method to generate a pretty formatted date for tooltips
  static getPrettyDate(value: string): string {
    if (value === undefined || value === ""){
      return "";
    }

    try {
      var days = ['Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag'];
      const months = ['Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December'];

      var parseddate = Date.parse(value.replace(/ /g,"T"));
      const d = new Date(parseddate);

      const dayName = days[d.getDay()];
      const monthName = months[d.getMonth()];
      const dayDigit = d.getDate();
      const h = DateTimePrettyPrintPipe.addZeroStatic(d.getHours());
      const m = DateTimePrettyPrintPipe.addZeroStatic(d.getMinutes());

      return `${dayName} ${dayDigit} ${monthName} ${d.getFullYear()} ${h}:${m}`;
    } catch (e) {
      console.error('Error generating pretty date:', e);
      return "";
    }
  }

  private addZero(i) {
    if (i < 10) {
      i = "0" + i;
    }
    return i;
  }

  // Static version of addZero for use in static methods
  private static addZeroStatic(i) {
    if (i < 10) {
      i = "0" + i;
    }
    return i;
  }
}
