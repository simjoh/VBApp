import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AssetService {

  /**
   * Get the full URL for an asset file
   * @param assetPath - The relative path to the asset (e.g., 'images/logo.svg')
   * @returns The full URL to the asset
   */
  getAssetUrl(assetPath: string): string {
    // Remove leading slash if present to avoid double slashes
    const cleanPath = assetPath.startsWith('/') ? assetPath.substring(1) : assetPath;
    return `${environment.assets_url}${cleanPath}`;
  }

  /**
   * Get the logo URL
   * @returns The full URL to the logo
   */
  getLogoUrl(): string {
    return environment.logo_path;
  }

  /**
   * Get the base assets URL
   * @returns The base assets URL
   */
  getAssetsBaseUrl(): string {
    return environment.assets_url;
  }

  /**
   * Get a checkpoint/site logo URL from the backend uploads
   * @param filename - The filename of the logo (e.g., 'circle-k.svg')
   * @returns The full URL to the checkpoint logo
   */
  getCheckpointLogoUrl(filename: string): string {
    if (!filename) {
      return this.getLogoUrl(); // Fallback to main logo
    }

    // Remove leading slash if present
    let cleanFilename = filename.startsWith('/') ? filename.substring(1) : filename;

    // Remove 'app' prefix if present (e.g., 'appvannasby-2.svg' -> 'vannasby-2.svg')
    if (cleanFilename.startsWith('app') && cleanFilename.length > 3) {
      cleanFilename = cleanFilename.substring(3);
    }

    // Ensure pictureurl ends with proper separator
    const baseUrl = environment.pictureurl.endsWith('/')
      ? environment.pictureurl.slice(0, -1)
      : environment.pictureurl;

    return `${baseUrl}/${cleanFilename}`;
  }
}
