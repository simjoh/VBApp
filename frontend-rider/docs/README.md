# VBApp Frontend Rider

Angular-based frontend application for riders participating in VBApp (Västerbottenbrevet) cycling events.

## Overview

This is the rider-facing frontend application built with Angular, providing an interface for cyclists to register for events, manage their participation, and access event information.

## Features

- **Event Registration**: Register for cycling events
- **Profile Management**: Manage personal information and preferences
- **Event Information**: View event details and requirements
- **Registration Status**: Track registration and payment status
- **Mobile Optimized**: Responsive design for mobile devices

## Prerequisites

- Node.js (v16 or higher)
- npm or yarn
- Angular CLI

## Quick Start

### Installation

1. **Install dependencies**
   ```bash
   npm install
   ```

2. **Start development server**
   ```bash
   ng serve
   ```

3. **Open browser**
   Navigate to `http://localhost:4200`

### Build for Production

```bash
ng build --prod
```

## Development

### Available Scripts

- `ng serve` - Start development server
- `ng build` - Build for production
- `ng test` - Run unit tests
- `ng e2e` - Run end-to-end tests
- `ng lint` - Run linting

## Configuration

### Environment Variables

Configure your environment in `src/environments/`:

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8090/api',
  loppserviceUrl: 'http://localhost:8082/loppservice/api'
};
```

## License

This project is licensed under the MIT License.
