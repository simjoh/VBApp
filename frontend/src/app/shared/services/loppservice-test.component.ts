import { Component, OnInit } from '@angular/core';
import { LoppserviceApiService } from './loppservice-api.service';

interface JwtTestResponse {
  message: string;
  user_id: string | null;
  organizer_id: number | null;
  roles: string[];
  timestamp: string;
}

@Component({
  selector: 'app-loppservice-test',
  template: `
    <div class="container mt-4">
      <h3>Loppservice JWT Test</h3>

      <div class="mb-3">
        <button class="btn btn-primary" (click)="testJwtValidation()" [disabled]="loading">
          {{ loading ? 'Testing...' : 'Test JWT Validation' }}
        </button>
        <button class="btn btn-secondary ms-2" (click)="testWithoutToken()" [disabled]="loading">
          Test Without Token
        </button>
      </div>

      <div *ngIf="result" class="alert" [ngClass]="result.success ? 'alert-success' : 'alert-danger'">
        <h5>{{ result.success ? 'Success!' : 'Error!' }}</h5>
        <pre>{{ result.data | json }}</pre>
      </div>

      <div *ngIf="error" class="alert alert-danger">
        <h5>Error Details:</h5>
        <pre>{{ error | json }}</pre>
      </div>
    </div>
  `,
  styles: [`
    .container {
      max-width: 800px;
    }
    pre {
      background-color: #f8f9fa;
      padding: 10px;
      border-radius: 4px;
      font-size: 12px;
    }
  `]
})
export class LoppserviceTestComponent implements OnInit {
  loading = false;
  result: { success: boolean; data: any } | null = null;
  error: any = null;

  constructor(private loppserviceApi: LoppserviceApiService) {}

  ngOnInit() {
    console.log('LoppserviceTestComponent initialized');
    console.log('Current token in localStorage:', localStorage.getItem('loggedInUser'));
  }

  testJwtValidation() {
    this.loading = true;
    this.result = null;
    this.error = null;

    console.log('Testing JWT validation with token...');

    this.loppserviceApi.get<JwtTestResponse>('api/pingjwt').subscribe({
      next: (response) => {
        console.log('JWT test successful:', response);
        this.result = {
          success: true,
          data: response
        };
        this.loading = false;
      },
      error: (error) => {
        console.error('JWT test failed:', error);
        this.error = error;
        this.loading = false;
      }
    });
  }

  testWithoutToken() {
    // Temporarily remove token to test without authentication
    const originalToken = localStorage.getItem('loggedInUser');
    localStorage.removeItem('loggedInUser');

    this.loading = true;
    this.result = null;
    this.error = null;

    console.log('Testing without token...');

    this.loppserviceApi.get<JwtTestResponse>('api/pingjwt').subscribe({
      next: (response) => {
        console.log('Test without token successful:', response);
        this.result = {
          success: true,
          data: response
        };
        this.loading = false;

        // Restore original token
        if (originalToken) {
          localStorage.setItem('loggedInUser', originalToken);
        }
      },
      error: (error) => {
        console.error('Test without token failed:', error);
        this.error = error;
        this.loading = false;

        // Restore original token
        if (originalToken) {
          localStorage.setItem('loggedInUser', originalToken);
        }
      }
    });
  }
}

