import { Injectable } from '@angular/core';
import {Observable, Subject} from 'rxjs';
import { HttpClient, HttpEventType, HttpRequest, HttpResponse } from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class UploadService {

  constructor(private http: HttpClient) { }


  public upload(url: string, files: Set<File>):
    { [key: string]: { progress: Observable<number>, response: Observable<any> } } {

    // this will be the our resulting map
    const status: { [key: string]: { progress: Observable<number>, response: Observable<any> } } = {};

    files.forEach(file => {
      // create a new multipart-form for every file
      const formData: FormData = new FormData();
      formData.append('file', file, file.name);

      // create a http-post request and pass the form
      // tell it to report the upload progress
      const req = new HttpRequest('POST', url + '?test=fantastiskt', formData, {
        reportProgress: true
      });

      // create a new progress-subject for every file
      const progress = new Subject<number>();
      const response = new Subject<any>();

      // send the http-request and subscribe for progress-updates
      this.http.request(req).subscribe(event => {
        if (event.type === HttpEventType.UploadProgress) {

          // calculate the progress percentage
          const percentDone = Math.round(100 * event.loaded / event.total);

          // pass the percentage into the progress-stream
          progress.next(percentDone);
        } else if (event instanceof HttpResponse) {

          // Close the progress-stream if we get an answer form the API
          // The upload is complete
          progress.complete();

          // Pass the response data
          response.next(event.body);
          response.complete();
        }
      });

      // Save every progress-observable in a map of all observables
      status[file.name] = {
        progress: progress.asObservable(),
        response: response.asObservable()
      };
    });

    // return the map of progress.observables
    return status;
  }
}
