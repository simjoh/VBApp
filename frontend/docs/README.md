# VBApp Frontend

Angular-based frontend application for the VBApp (Västerbottenbrevet) cycling event management system.

## Overview

This is the main frontend application built with Angular, providing a comprehensive user interface for managing cycling events, participant registrations, and event administration.

## Features

- **Event Management**: Create and manage cycling events
- **Participant Registration**: Handle participant registrations and tracking
- **Event Statistics**: Real-time statistics and analytics
- **Responsive Design**: Mobile-friendly interface
- **Modern UI/UX**: Clean and intuitive user experience

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
