# Loppservice API Integration

This directory contains the service for making REST API calls to the loppservice backend.

## Files

- `loppservice-api.service.ts` - Main service for making HTTP requests to loppservice
- `loppservice-example.component.ts` - Example component demonstrating usage
- `README.md` - This documentation file

## Configuration

The loppservice URL is configured in the environment files:
- Development: `/loppservice/` (proxied to `http://localhost:8082`)
- Production: `/loppservice/` (proxied to the production loppservice)

## Usage

### 1. Import the Service

```typescript
import { LoppserviceApiService } from '../shared/services/loppservice-api.service';

@Component({...})
export class MyComponent {
  constructor(private loppserviceApi: LoppserviceApiService) {}
}
```

### 2. Make API Calls

#### GET Request
```typescript
this.loppserviceApi.get<ResponseType>('endpoint').subscribe({
  next: (response) => {
    console.log('Response:', response);
  },
  error: (error) => {
    console.error('Error:', error);
  }
});
```

#### POST Request
```typescript
const data = { key: 'value' };
this.loppserviceApi.post<ResponseType>('endpoint', data).subscribe({
  next: (response) => {
    console.log('Response:', response);
  },
  error: (error) => {
    console.error('Error:', error);
  }
});
```

#### PUT Request
```typescript
const data = { key: 'updated_value' };
this.loppserviceApi.put<ResponseType>('endpoint', data).subscribe({
  next: (response) => {
    console.log('Response:', response);
  },
  error: (error) => {
    console.error('Error:', error);
  }
});
```

#### DELETE Request
```typescript
this.loppserviceApi.delete<ResponseType>('endpoint').subscribe({
  next: (response) => {
    console.log('Response:', response);
  },
  error: (error) => {
    console.error('Error:', error);
  }
});
```

#### PATCH Request
```typescript
const data = { key: 'partial_update' };
this.loppserviceApi.patch<ResponseType>('endpoint', data).subscribe({
  next: (response) => {
    console.log('Response:', response);
  },
  error: (error) => {
    console.error('Error:', error);
  }
});
```

### 3. Custom Headers

For requests that need custom headers:

```typescript
import { HttpHeaders } from '@angular/common/http';

const customHeaders = new HttpHeaders({
  'Custom-Header': 'value'
});

this.loppserviceApi.request<ResponseType>('GET', 'endpoint', null, customHeaders).subscribe({
  next: (response) => {
    console.log('Response:', response);
  },
  error: (error) => {
    console.error('Error:', error);
  }
});
```

## Authentication

The service automatically includes:
- API Key header (`APIKEY: notsecret_developer_key`)
- Token header (`TOKEN: <user_token>`) if user is logged in
- Authorization header (`Authorization: Bearer <user_token>`) if user is logged in

These are handled by the existing interceptors in the application.

## Error Handling

All methods include error handling with console logging. Errors are re-thrown so you can handle them in your components as needed.

## Example Endpoints

Based on the existing loppservice setup, you can make calls to endpoints like:
- `health` - Health check
- `participants` - Participant management
- `tracks` - Track management
- `events` - Event management

## Proxy Configuration

The development proxy is configured in `proxy.conf.json`:
```json
{
  "/loppservice": {
    "target": "http://localhost:8082",
    "secure": false,
    "changeOrigin": true,
    "logLevel": "debug"
  }
}
```

This means all requests to `/loppservice/*` will be proxied to `http://localhost:8082/*`.
