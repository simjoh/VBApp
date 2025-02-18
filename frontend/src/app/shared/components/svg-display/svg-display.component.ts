import {
  ChangeDetectionStrategy,
  Component,
  Input,
  OnInit,
} from '@angular/core';
import {SvgService} from '../../svg.service';
import {DomSanitizer, SafeHtml} from '@angular/platform-browser';
import {NgStyle} from '@angular/common';

@Component({
    selector: 'brevet-svg-display',
    templateUrl: './svg-display.component.html',
    styleUrls: ['./svg-display.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class SvgDisplayComponent implements OnInit {
  @Input() svgID!: number; // Changed to `number` for TypeScript type consistency
  @Input() svgWidth: number = 5; // Changed to `number` for TypeScript type consistency
  @Input() svgHeight: number = 5; // Changed to `number` for TypeScript type consistency

  $svgContent: SafeHtml | null = null; // Type explicitly as SafeHtml for sanitized content

  constructor(private svgcache: SvgService, private sanitizer: DomSanitizer) {


  }

  ngOnInit() {
    this.loadData();
  }

  private loadData() {
    const rawSvg = this.svgcache.get(this.svgID.toString()); // Fetch raw SVG content
    this.updateSvgDimensions(rawSvg);
  }

  private updateSvgDimensions(svg: string | null) {
    if (!svg) {
      console.error('SVG content is null or undefined');
      return;
    }

    const parser = new DOMParser();
    const doc = parser.parseFromString(svg, 'image/svg+xml');
    const svgElement = doc.querySelector('svg');

    if (svgElement) {
      // Set the width and height dynamically
      svgElement.setAttribute('width', this.svgWidth.toString());
      svgElement.setAttribute('height', this.svgHeight.toString());

      // Serialize and sanitize the updated SVG for rendering
      const updatedSvg = new XMLSerializer().serializeToString(svgElement);
      this.$svgContent = this.sanitizer.bypassSecurityTrustHtml(updatedSvg);
    } else {
      console.error('No SVG element found in the provided content.');
    }
  }
}
