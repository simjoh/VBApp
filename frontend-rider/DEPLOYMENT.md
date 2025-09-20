# Frontend Rider - Production Deployment Guide

## The Problem
When refreshing the page on routes like `https://www.ebrevet.org/rider/dashboard`, you get a 404 error. This happens because the web server tries to find a physical file at that path, but Angular handles routing client-side.

## Solution
Configure your web server to serve `index.html` for all routes that don't match physical files.

## Build for Production
```bash
ng build --configuration=production
```

## Web Server Configuration

### Apache (Current Setup)
The `.htaccess` file in the `public/` folder will be included in the build. Ensure Apache has mod_rewrite enabled.

### Option 3: Other Web Servers

#### Express.js
```javascript
const express = require('express');
const path = require('path');
const app = express();

app.use(express.static(path.join(__dirname, 'dist/frontend-rider/browser')));

// Handle Angular routing
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'dist/frontend-rider/browser/index.html'));
});

app.listen(80);
```

#### IIS (web.config)
```xml
<?xml version="1.0" encoding="utf-8"?>
<configuration>
  <system.webServer>
    <rewrite>
      <rules>
        <rule name="Angular Routes" stopProcessing="true">
          <match url=".*" />
          <conditions logicalGrouping="MatchAll">
            <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
          </conditions>
          <action type="Rewrite" url="/index.html" />
        </rule>
      </rules>
    </rewrite>
  </system.webServer>
</configuration>
```

## Deployment Checklist

1. ✅ Build with production configuration
2. ✅ Configure web server for SPA routing
3. ✅ Set correct base href (`/rider/`)
4. ✅ Ensure static assets are served from correct path
5. ✅ Test all routes work on refresh

## Testing
After deployment, test these URLs by refreshing:
- `https://www.ebrevet.org/app/rider/`
- `https://www.ebrevet.org/app/rider/login`
- `https://www.ebrevet.org/app/rider/dashboard`
- `https://www.ebrevet.org/app/rider/geolocation-permission`

All should serve the Angular app, not 404 errors.
