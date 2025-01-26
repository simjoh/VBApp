import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from "../../environments/environment";
import { map } from 'rxjs/operators';
import { CacheRepresentation } from "./api/api";

@Injectable({
  providedIn: 'root',
})
export class SvgService {
  private baseUrl = environment.backend_url;
  private cache = new Map<string, string>();

  constructor(private http: HttpClient) {}

  preloadSvgs(): Promise<void> {
    if (this.cache.size > 0) {
      return Promise.resolve(); // Already preloaded
    }

    return this.http
      .get<CacheRepresentation[]>(`${this.baseUrl}cacheSvgs`)
      .pipe(
        map((response) => {
          response.forEach((s) => {
            this.cache.set(s.id.toString(), this.decodeBase64Svg(s.svg_blob));
          });
        })
      )
      .toPromise()
      .then(() => {
        console.log('SVGs preloaded successfully');
      })
      .catch((err) => {
        console.error('Error preloading SVGs', err);
      });
  }

  private decodeBase64Svg(base64Svg: string): string {
    return atob(base64Svg); // Decode base64-encoded SVG content
  }

  get(key: string): string | undefined {
    return this.cache.get(key);
  }
}
