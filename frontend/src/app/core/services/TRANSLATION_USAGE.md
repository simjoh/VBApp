# Translation Usage Guide

This guide explains how to use the Swedish and English translation system in the frontend application.

## Overview

The application supports two languages:
- **Swedish (sv)** - Svenska (Default language)
- **English (en)** - English

## How to Use Translations

### 1. In Templates (HTML)

Use the `translate` pipe to translate text:

```html
<!-- Simple translation -->
<h1>{{ 'login.title' | translate }}</h1>

<!-- Translation with property binding -->
<input [placeholder]="'form.enterClubName' | translate">

<!-- Translation in button labels -->
<button [label]="'common.save' | translate"></button>
```

### 2. In Components (TypeScript)

Inject the `TranslationService` and use the `translate` method:

```typescript
import { TranslationService } from '../core/services/translation.service';

export class MyComponent {
  constructor(private translationService: TranslationService) {}

  getTranslatedText() {
    return this.translationService.translate('common.loading');
  }
}
```

### 3. For Login Screen (Browser Language)

Use the `displayTranslate` pipe for login screen elements that should use browser language:

```html
<!-- This will use browser language, not saved preference -->
<div>{{ 'login.title' | displayTranslate }}</div>
```

## Available Translation Keys

### Common Actions
- `common.save` - Save
- `common.cancel` - Cancel
- `common.edit` - Edit
- `common.delete` - Delete
- `common.add` - Add
- `common.close` - Close
- `common.back` - Back
- `common.next` - Next
- `common.previous` - Previous
- `common.search` - Search
- `common.filter` - Filter
- `common.sort` - Sort
- `common.refresh` - Refresh
- `common.export` - Export
- `common.import` - Import
- `common.print` - Print
- `common.download` - Download
- `common.upload` - Upload
- `common.update` - Update
- `common.create` - Create
- `common.view` - View
- `common.details` - Details
- `common.actions` - Actions
- `common.select` - Select
- `common.clear` - Clear
- `common.reset` - Reset
- `common.submit` - Submit
- `common.continue` - Continue
- `common.finish` - Finish
- `common.start` - Start
- `common.stop` - Stop
- `common.pause` - Pause
- `common.resume` - Resume
- `common.retry` - Retry
- `common.undo` - Undo
- `common.redo` - Redo
- `common.copy` - Copy
- `common.paste` - Paste
- `common.cut` - Cut
- `common.selectAll` - Select All
- `common.none` - None
- `common.all` - All
- `common.optional` - Optional
- `common.required` - Required

### Navigation
- `nav.dashboard` - Dashboard
- `nav.admin` - Admin
- `nav.competitor` - Competitor
- `nav.volunteer` - Volunteer
- `nav.login` - Login
- `nav.logout` - Logout
- `nav.profile` - Profile
- `nav.settings` - Settings
- `nav.overview` - Overview
- `nav.participants` - Participants
- `nav.tracks` - Tracks
- `nav.events` - Events
- `nav.reports` - Reports
- `nav.users` - Users
- `nav.clubs` - Clubs
- `nav.organizers` - Organizers
- `nav.checkpoints` - Checkpoints
- `nav.upload` - Upload
- `nav.uploadParticipants` - Upload Participants
- `nav.createNew` - Create New
- `nav.createNewEvent` - Create New Event
- `nav.createNewTrack` - Create New Track
- `nav.createNewUser` - Create New User
- `nav.createNewClub` - Create New Club
- `nav.createNewOrganizer` - Create New Organizer
- `nav.createNewCheckpoint` - Create New Checkpoint
- `nav.gpxImport` - GPX Import
- `nav.copyTrack` - Copy Track
- `nav.acpReport` - ACP Report
- `nav.system` - System

### Login
- `login.title` - Västerbottenbrevet
- `login.subtitle` - Admin Portal
- `login.username` - Username
- `login.password` - Password
- `login.loginButton` - Login
- `login.error.invalidCredentials` - Invalid username or password
- `login.error.required` - This field is required

### Admin
- `admin.dashboard` - Admin Dashboard
- `admin.users` - Users
- `admin.events` - Events
- `admin.tracks` - Tracks
- `admin.participants` - Participants
- `admin.reports` - Reports
- `admin.settings` - Settings
- `admin.panel` - Admin Panel
- `admin.overview` - Overview
- `admin.participantList` - Participant List
- `admin.uploadParticipants` - Upload Participants
- `admin.trackList` - Track List
- `admin.createNewEvent` - Create New Event
- `admin.gpxImport` - GPX Import
- `admin.copyTrack` - Copy Track
- `admin.acpReport` - ACP Report
- `admin.system` - System
- `admin.clubs` - Clubs
- `admin.organizers` - Organizers
- `admin.checkpoints` - Checkpoints

### Competitor
- `competitor.title` - Competitor Portal
- `competitor.startNumber` - Start Number
- `competitor.riderName` - Rider Name
- `competitor.trackInfo` - Track Information
- `competitor.checkpoints` - Checkpoints
- `competitor.status` - Status
- `competitor.time` - Time
- `competitor.distance` - Distance
- `competitor.checkIn` - Check In
- `competitor.checkOut` - Check Out
- `competitor.undoCheckIn` - Undo Check In
- `competitor.undoCheckOut` - Undo Check Out
- `competitor.dnf` - DNF
- `competitor.undoDnf` - Undo DNF
- `competitor.checkedIn` - Checked In
- `competitor.checkedOut` - Checked Out
- `competitor.service` - Service
- `competitor.opens` - Opens
- `competitor.closes` - Closes
- `competitor.open` - Open
- `competitor.closed` - Closed
- `competitor.toNext` - To Next
- `competitor.finish` - Finish
- `competitor.start` - Start
- `competitor.checkin` - Check In

### Volunteer
- `volunteer.title` - Volunteer Control
- `volunteer.checkpoints` - Checkpoints
- `volunteer.participants` - Participants
- `volunteer.tracks` - Tracks
- `volunteer.events` - Events
- `volunteer.control` - Control
- `volunteer.manageParticipants` - Manage Participants at Your Checkpoint
- `volunteer.help` - Help and Instructions
- `volunteer.instructions` - Instructions
- `volunteer.howItWorks` - How does it work?
- `volunteer.toSeeParticipants` - To see participants at your checkpoint:
- `volunteer.selectEventAndTrack` - Select event and track in the side panel
- `volunteer.selectYourCheckpoint` - Select your checkpoint from the list
- `volunteer.seeParticipants` - See participants who should pass or have passed
- `volunteer.asVolunteer` - As a volunteer you can:
- `volunteer.checkInParticipants` - Check in participants
- `volunteer.markDnf` - Mark DNF (Did Not Finish)
- `volunteer.undoActions` - Undo previous actions
- `volunteer.event` - Event
- `volunteer.checkpoint` - Checkpoint
- `volunteer.expected` - Expected
- `volunteer.remaining` - Remaining
- `volunteer.checkedIn` - Checked In
- `volunteer.checkedOut` - Checked Out
- `volunteer.dnf` - DNF
- `volunteer.undoCheckin` - Undo Check In
- `volunteer.undoCheckout` - Undo Check Out
- `volunteer.undoDnf` - Undo DNF
- `volunteer.checkout` - Check Out

### Forms
- `form.required` - This field is required
- `form.invalid` - Invalid value
- `form.email` - Email
- `form.password` - Password
- `form.confirmPassword` - Confirm Password
- `form.name` - Name
- `form.description` - Description
- `form.date` - Date
- `form.time` - Time
- `form.location` - Location
- `form.phone` - Phone
- `form.address` - Address
- `form.city` - City
- `form.country` - Country
- `form.zipCode` - ZIP Code
- `form.firstName` - First Name
- `form.lastName` - Last Name
- `form.username` - Username
- `form.organizationName` - Organization Name
- `form.contactPersonName` - Contact Person Name
- `form.contactPersonEmail` - Contact Person Email
- `form.contactPersonPhone` - Contact Person Phone
- `form.clubName` - Club Name
- `form.acpCode` - ACP Code
- `form.acpCodeOptional` - ACP Code (Optional)
- `form.leaveEmptyIfNoAcpCode` - Leave empty if no ACP code
- `form.leaveEmptyIfNoAcpCodeDescription` - Leave empty if the club does not have an ACP code
- `form.eventName` - Event Name
- `form.eventDescription` - Event Description
- `form.trackName` - Track Name
- `form.trackDescription` - Track Description
- `form.checkpointName` - Checkpoint Name
- `form.checkpointAddress` - Checkpoint Address
- `form.checkpointDescription` - Checkpoint Description
- `form.enterClubName` - Enter club name
- `form.enterFirstName` - Enter first name
- `form.enterLastName` - Enter last name
- `form.enterUsername` - Enter username
- `form.enterOrganizationName` - Enter organization name
- `form.enterContactPersonName` - Enter contact person name
- `form.enterEventName` - Enter event name
- `form.enterEventDescription` - Enter event description
- `form.enterTrackName` - Enter track name
- `form.enterTrackDescription` - Enter track description
- `form.enterCheckpointName` - Enter checkpoint name
- `form.enterCheckpointAddress` - Enter checkpoint address
- `form.enterCheckpointDescription` - Enter checkpoint description
- `form.userInformation` - User Information
- `form.contactInformation` - Contact Information
- `form.eventInformation` - Event Information
- `form.trackInformation` - Track Information
- `form.checkpointInformation` - Checkpoint Information
- `form.clubInformation` - Club Information
- `form.organizationInformation` - Organization Information

### Validation Messages
- `validation.required` - This field is required
- `validation.invalid` - Invalid value
- `validation.email` - Invalid email format
- `validation.minLength` - Minimum length not met
- `validation.maxLength` - Maximum length exceeded
- `validation.pattern` - Invalid format
- `validation.unique` - Value must be unique
- `validation.clubNameRequired` - Club name is required
- `validation.firstNameRequired` - First name is required
- `validation.lastNameRequired` - Last name is required
- `validation.usernameRequired` - Username is required
- `validation.organizationNameRequired` - Organization name is required
- `validation.contactPersonNameRequired` - Contact person name is required
- `validation.eventNameRequired` - Event name is required
- `validation.trackNameRequired` - Track name is required
- `validation.checkpointNameRequired` - Checkpoint name is required

### Messages
- `message.success` - Operation completed successfully
- `message.error` - An error occurred
- `message.warning` - Warning
- `message.info` - Information
- `message.loading` - Loading...
- `message.noData` - No data available
- `message.connectionError` - Connection error
- `message.unauthorized` - Unauthorized access
- `message.forbidden` - Access forbidden
- `message.notFound` - Not found
- `message.serverError` - Server error
- `message.saved` - Saved successfully
- `message.updated` - Updated successfully
- `message.deleted` - Deleted successfully
- `message.created` - Created successfully
- `message.confirmDelete` - Are you sure you want to delete this item?
- `message.confirmAction` - Are you sure you want to perform this action?
- `message.operationSuccessful` - Operation completed successfully
- `message.operationFailed` - Operation failed
- `message.pleaseWait` - Please wait...
- `message.areYouSure` - Are you sure?
- `message.thisActionCannotBeUndone` - This action cannot be undone
- `message.dataSaved` - Data saved successfully
- `message.dataUpdated` - Data updated successfully
- `message.dataDeleted` - Data deleted successfully
- `message.dataCreated` - Data created successfully

### Status
- `status.active` - Active
- `status.inactive` - Inactive
- `status.pending` - Pending
- `status.completed` - Completed
- `status.cancelled` - Cancelled
- `status.archived` - Archived
- `status.draft` - Draft
- `status.published` - Published
- `status.registered` - Registered
- `status.unregistered` - Unregistered
- `status.checkedIn` - Checked In
- `status.checkedOut` - Checked Out
- `status.dnf` - DNF
- `status.finished` - Finished
- `status.started` - Started
- `status.stopped` - Stopped
- `status.paused` - Paused
- `status.resumed` - Resumed

### Coming Soon
- `comingSoon.gpxImport` - GPX Import
- `comingSoon.copyTrack` - Copy Track
- `comingSoon.badge` - Coming Soon

### Dashboard/Overview
- `dashboard.welcome` - Welcome to eBrevet
- `dashboard.welcomeBack` - Welcome back! Here is your overview
- `dashboard.overview` - Overview
- `dashboard.overviewAndStats` - Overview and statistics
- `dashboard.totalParticipants` - Total participants
- `dashboard.activeTracks` - Active tracks
- `dashboard.activeEvents` - Active events
- `dashboard.completed` - Completed
- `dashboard.registered` - Registered
- `dashboard.dns` - DNS
- `dashboard.latestRegistration` - Latest registration
- `dashboard.topPerformingTracks` - Top performing tracks
- `dashboard.mostPopularTracks` - Most popular tracks (Last 30 days)
- `dashboard.todaysTracks` - Today's tracks
- `dashboard.tracksRunningToday` - Tracks running today
- `dashboard.last7Days` - Last 7 days
- `dashboard.today` - Today
- `dashboard.thisYear` - This year
- `dashboard.total` - Total
- `dashboard.tracks` - tracks
- `dashboard.events` - events
- `dashboard.participants` - participants
- `dashboard.addNewTrack` - Add new track
- `dashboard.activeEventsRunning` - Active events running
- `dashboard.didNotStart` - Did not start
- `dashboard.totalRegistered` - Total registered
- `dashboard.newRegistration` - New registration
- `dashboard.noRegistrationsYet` - No registrations yet
- `dashboard.noTracksLast30Days` - No tracks with participants in the last 30 days
- `dashboard.noTracksRunningToday` - No tracks running today
- `dashboard.heightDifference` - m height difference
- `dashboard.developedBy` - Developed by
- `dashboard.dailyParticipants` - Daily participants
- `dashboard.startedToday` - Started today
- `dashboard.brokeToday` - Broke today
- `dashboard.didNotStartToday` - Did not start today
- `dashboard.weekly` - Weekly
- `dashboard.eventGroup` - Event Group
- `dashboard.manageEventGroups` - Manage event groups
- `dashboard.manageTracks` - Manage tracks
- `dashboard.manageParticipants` - Manage participants
- `dashboard.manageClubs` - Manage clubs
- `dashboard.managePlaces` - Manage places
- `dashboard.manageCheckpoints` - Manage checkpoints
- `dashboard.manageUsers` - Manage users
- `dashboard.manageOrganizers` - Manage organizers

## Adding New Translations

To add new translation keys:

1. Add the key to the `TranslationKeys` interface in `translation.service.ts`
2. Add the translations for all 4 languages in the `translations` object
3. Use the key in your templates or components

## Language Selection

The language selector appears:
- **Admin users**: In the sidebar header
- **Competitor users**: In the top header
- **Login screen**: Uses browser language automatically (no selector shown)

Users can switch languages and their preference is saved to localStorage.
