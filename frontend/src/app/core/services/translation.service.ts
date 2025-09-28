import { Injectable, inject } from '@angular/core';
import { LanguageService, SupportedLanguage } from './language.service';

export interface TranslationKeys {
  // Common
  'common.loading': string;
  'common.error': string;
  'common.success': string;
  'common.cancel': string;
  'common.map': string;
  'common.confirm': string;
  'common.summary': string;
  'common.yes': string;
  'common.no': string;
  'common.save': string;
  'common.edit': string;
  'common.delete': string;
  'common.add': string;
  'common.close': string;
  'common.back': string;
  'common.next': string;
  'common.previous': string;
  'common.search': string;
  'common.filter': string;
  'common.sort': string;
  'common.refresh': string;
  'common.export': string;
  'common.import': string;
  'common.print': string;
  'common.download': string;
  'common.upload': string;
  'common.update': string;
  'common.create': string;
  'common.view': string;
  'common.details': string;
  'common.actions': string;
  'common.select': string;
  'common.clear': string;
  'common.reset': string;
  'common.submit': string;
  'common.continue': string;
  'common.finish': string;
  'common.start': string;
  'common.stop': string;
  'common.pause': string;
  'common.resume': string;
  'common.retry': string;
  'common.undo': string;
  'common.redo': string;
  'common.copy': string;
  'common.paste': string;
  'common.cut': string;
  'common.selectAll': string;
  'common.none': string;
  'common.all': string;
  'common.optional': string;
  'common.required': string;
  'common.and': string;
  'common.to': string;

  // Navigation
  'nav.dashboard': string;
  'nav.tracks': string;
  'nav.trackList': string;
  'nav.participants': string;
  'nav.events': string;
  'nav.reports': string;
  'nav.clubs': string;
  'nav.checkpoints': string;
  'nav.users': string;
  'nav.organizers': string;
  'nav.settings': string;
  'nav.profile': string;
  'nav.logout': string;
  'nav.home': string;
  'nav.admin': string;
  'nav.volunteer': string;
  'nav.competitor': string;
  'nav.participantList': string;
  'nav.uploadParticipants': string;
  'nav.createNewEvent': string;
  'nav.gpxImport': string;
  'nav.copyTrack': string;
  'nav.reportToAcp': string;
  'nav.system': string;

  // Login
  'login.title': string;
  'login.username': string;
  'login.password': string;
  'login.loginButton': string;
  'login.forgotPassword': string;
  'login.rememberMe': string;
  'login.invalidCredentials': string;
  'login.loginSuccess': string;
  'login.loginError': string;
  'login.competitorNotAllowed': string;

  // Admin
  'admin.dashboard': string;
  'admin.overview': string;
  'admin.statistics': string;
  'admin.management': string;
  'admin.settings': string;
  'admin.users': string;
  'admin.permissions': string;
  'admin.roles': string;
  'admin.audit': string;

  // Competitor
  'competitor.dashboard': string;
  'competitor.myTracks': string;
  'competitor.myResults': string;
  'competitor.profile': string;
  'competitor.registration': string;
  'competitor.trackSelection': string;
  'competitor.payment': string;
  'competitor.confirmation': string;

  // Volunteer
  'volunteer.dashboard': string;
  'volunteer.checkpoints': string;
  'volunteer.participants': string;
  'volunteer.tracking': string;
  'volunteer.reports': string;
  'volunteer.schedule': string;
  'volunteer.tasks': string;

  // Forms
  'form.name': string;
  'form.email': string;
  'form.phone': string;
  'form.address': string;
  'form.city': string;
  'form.postalCode': string;
  'form.country': string;
  'form.date': string;
  'form.time': string;
  'form.dateTime': string;
  'form.startDate': string;
  'form.endDate': string;
  'form.duration': string;
  'form.distance': string;
  'form.elevation': string;
  'form.difficulty': string;
  'form.description': string;
  'form.notes': string;
  'form.status': string;
  'form.type': string;
  'form.category': string;
  'form.priority': string;
  'form.tags': string;
  'form.file': string;
  'form.image': string;
  'form.document': string;
  'form.clubName': string;
  'form.enterClubName': string;
  'form.acpCodeOptional': string;
  'form.leaveEmptyIfNoAcpCode': string;
  'form.leaveEmptyIfNoAcpCodeDescription': string;

  // Validation
  'validation.required': string;
  'validation.email': string;
  'validation.phone': string;
  'validation.minLength': string;
  'validation.maxLength': string;
  'validation.min': string;
  'validation.max': string;
  'validation.pattern': string;
  'validation.confirmPassword': string;
  'validation.unique': string;
  'validation.date': string;
  'validation.time': string;
  'validation.futureDate': string;
  'validation.pastDate': string;
  'validation.clubNameRequired': string;

  // Messages
  'message.success': string;
  'message.error': string;
  'message.warning': string;
  'message.info': string;
  'message.confirm': string;
  'message.unsavedChanges': string;
  'message.dataSaved': string;
  'message.dataUpdated': string;
  'message.dataDeleted': string;
  'message.dataLoaded': string;
  'message.operationFailed': string;
  'message.networkError': string;
  'message.serverError': string;
  'message.unauthorized': string;
  'message.forbidden': string;
  'message.notFound': string;
  'message.timeout': string;
  'message.retry': string;

  // Status
  'status.active': string;
  'status.inactive': string;
  'status.pending': string;
  'status.approved': string;
  'status.rejected': string;
  'status.completed': string;
  'status.cancelled': string;
  'status.draft': string;
  'status.published': string;
  'status.archived': string;
  'status.deleted': string;
  'status.enabled': string;
  'status.disabled': string;
  'status.available': string;
  'status.unavailable': string;
  'status.online': string;
  'status.offline': string;
  'status.connected': string;
  'status.disconnected': string;
  'status.synced': string;
  'status.syncing': string;
  'status.started': string;
  'status.stopped': string;
  'status.paused': string;
  'status.resumed': string;

  // Coming Soon
  'comingSoon.gpxImport': string;
  'comingSoon.copyTrack': string;
  'comingSoon.badge': string;

  // Dashboard/Overview
  'dashboard.welcome': string;
  'dashboard.welcomeBack': string;
  'dashboard.overview': string;
  'dashboard.overviewAndStats': string;
  'dashboard.totalParticipants': string;
  'dashboard.activeTracks': string;
  'dashboard.activeEvents': string;
  'dashboard.completed': string;
  'dashboard.registered': string;
  'dashboard.dns': string;
  'dashboard.latestRegistration': string;
  'dashboard.topPerformingTracks': string;
  'dashboard.mostPopularTracks': string;
  'dashboard.todaysTracks': string;
  'dashboard.tracksRunningToday': string;
  'dashboard.last7Days': string;
  'dashboard.today': string;
  'dashboard.thisYear': string;
  'dashboard.total': string;
  'dashboard.tracks': string;
  'dashboard.events': string;
  'dashboard.participants': string;
  'dashboard.addNewTrack': string;
  'dashboard.activeEventsRunning': string;
  'dashboard.didNotStart': string;
  'dashboard.totalRegistered': string;
  'dashboard.newRegistration': string;
  'dashboard.noRegistrationsYet': string;
  'dashboard.noTracksLast30Days': string;
  'dashboard.noTracksRunningToday': string;
  'dashboard.heightDifference': string;
  'dashboard.developedBy': string;
  'dashboard.dailyParticipants': string;
  'dashboard.startedToday': string;
  'dashboard.brokeToday': string;
  'dashboard.didNotStartToday': string;
  'dashboard.weekly': string;
  'dashboard.eventGroup': string;
  'dashboard.manageEventGroups': string;
  'dashboard.manageTracks': string;
  'dashboard.manageParticipants': string;
  'dashboard.manageClubs': string;
  'dashboard.managePlaces': string;
  'dashboard.manageCheckpoints': string;
  'dashboard.manageUsers': string;
  'dashboard.manageOrganizers': string;

  // Participant Management
  'participant.manageParticipants': string;
  'participant.manageParticipantsDescription': string;
  'participant.participantList': string;
  'participant.participantListDescription': string;
  'participant.upload': string;
  'participant.uploadDescription': string;
  'participant.searchParticipants': string;
  'participant.exportStartList': string;
  'participant.exportHomologation': string;
  'participant.publishResults': string;
  'participant.unpublishResults': string;
  'participant.selectTrackFirst': string;
  'participant.trackMustBePublished': string;
  'participant.moveAllParticipants': string;
  'participant.startNumber': string;
  'participant.lastName': string;
  'participant.firstName': string;
  'participant.club': string;
  'participant.actions': string;
  'participant.move': string;
  'participant.moveTooltip': string;
  'participant.edit': string;
  'participant.delete': string;
  'participant.address': string;
  'participant.city': string;
  'participant.country': string;
  'participant.phone': string;
  'participant.email': string;
  'participant.finishTime': string;
  'participant.homologationNumber': string;
  'participant.noParticipantsFound': string;
  'participant.showingParticipants': string;

  // Checkpoint Table
  'checkpoint.logo': string;
  'checkpoint.address': string;
  'checkpoint.city': string;
  'checkpoint.distance': string;
  'checkpoint.checkins': string;
  'checkpoint.checkouts': string;
  'checkpoint.action': string;
  'checkpoint.changeTime': string;
  'checkpoint.undo': string;
  'checkpoint.checkin': string;
  'checkpoint.checkout': string;
  'checkpoint.undoCheckout': string;
  'checkpoint.selectTrackToSeeCheckpoints': string;

  // Edit Checkpoint Time Dialog
  'checkpointDialog.changeCheckinTime': string;
  'checkpointDialog.changeCheckoutTime': string;
  'checkpointDialog.enterDateAndTime': string;
  'checkpointDialog.cancel': string;
  'checkpointDialog.save': string;

  // Upload Participants
  'upload.uploadParticipants': string;
  'upload.uploadParticipantsDescription': string;
  'upload.csvFormatForParticipants': string;
  'upload.sixteenColumnsSeparatedBySemicolon': string;
  'upload.startNumber': string;
  'upload.firstName': string;
  'upload.lastName': string;
  'upload.gender': string;
  'upload.clubName': string;
  'upload.address': string;
  'upload.postalCode': string;
  'upload.city': string;
  'upload.country': string;
  'upload.email': string;
  'upload.phone': string;
  'upload.registrationDate': string;
  'upload.birthYear': string;
  'upload.referenceNumber': string;
  'upload.physicalBrevetCard': string;
  'upload.additionalInformation': string;
  'upload.example': string;
  'upload.trackActive': string;
  'upload.trackInactive': string;
  'upload.uploadNotAllowedForInactiveTracks': string;
  'upload.chooseCsv': string;
  'upload.uploadCsv': string;
  'upload.selectedFiles': string;
  'upload.uploadedFile': string;
  'upload.uploadResult': string;
  'upload.totalRows': string;
  'upload.successful': string;
  'upload.skipped': string;
  'upload.failed': string;
  'upload.detailedErrors': string;
  'upload.row': string;
  'upload.data': string;
  'upload.createdParticipants': string;
  'upload.participant': string;
  'upload.uploadComplete': string;
  'upload.uploadIssues': string;
  'upload.uploadFailed': string;

  // Track Management
  'track.manageTracks': string;
  'track.manageTracksDescription': string;
  'track.trackList': string;
  'track.trackListDescription': string;
  'track.trackBuilder': string;
  'track.trackBuilderDescription': string;
  'track.openTrackBuilder': string;
  'track.searchTracks': string;
  'track.event': string;
  'track.startDate': string;
  'track.start': string;
  'track.endDate': string;
  'track.end': string;
  'track.status': string;
  'track.trackingLink': string;
  'track.tracking': string;
  'track.action': string;
  'track.completed': string;
  'track.ongoing': string;
  'track.cancelled': string;
  'track.followParticipantsOnEvent': string;
  'track.remove': string;
  'track.noTracksFound': string;
  'track.noTracksToShow': string;
  'track.track': string;
  'track.trackLink': string;
  'track.date': string;
  'track.distance': string;
  'track.cyclistView': string;
  'track.trackingPage': string;
  'track.followParticipantsOnTrack': string;
  'track.noTracksOnEvent': string;
  'track.controls': string;

  // Checkpoint Table (General)
  'checkpointTable.logo': string;
  'checkpointTable.address': string;
  'checkpointTable.city': string;
  'checkpointTable.distance': string;
  'checkpointTable.opens': string;
  'checkpointTable.closes': string;
  'checkpointTable.noCheckpointsFound': string;
  'checkpointTable.noCheckpointsOnTrack': string;

  // Event Creation
  'event.createEvent': string;
  'event.addEvent': string;
  'event.title': string;
  'event.titleRequired': string;
  'event.titlePlaceholder': string;
  'event.startDate': string;
  'event.endDate': string;
  'event.status': string;
  'event.description': string;
  'event.descriptionPlaceholder': string;
  'event.statusActive': string;
  'event.statusCancelled': string;
  'event.statusCompleted': string;
  'event.cancel': string;
  'event.save': string;
  'event.newEvent': string;
  'event.searchEvents': string;
  'event.refresh': string;
  'event.name': string;
  'event.actions': string;
  'event.edit': string;
  'event.delete': string;
  'event.noEventsFound': string;
  'event.noEventsToShow': string;
  'event.completed': string;
  'event.ongoing': string;
  'event.cancelled': string;

  // Track Builder - Event Creation
  'trackBuilder.createNewEvent': string;
  'trackBuilder.createNewEventDescription': string;
  'trackBuilder.buildFromGpx': string;
  'trackBuilder.buildFromGpxDescription': string;
  'trackBuilder.copyExistingTrack': string;
  'trackBuilder.copyExistingTrackDescription': string;
  'trackBuilder.upcomingFeature': string;
  'trackBuilder.fillInformation': string;
  'trackBuilder.fillInformationDescription': string;

  // Track Builder - Track Info Form
  'trackInfo.eventGroup': string;
  'trackInfo.selectEventGroup': string;
  'trackInfo.organizer': string;
  'trackInfo.selectOrganizer': string;
  'trackInfo.basicInformation': string;
  'trackInfo.eventName': string;
  'trackInfo.eventNameTooltip': string;
  'trackInfo.eventNamePlaceholder': string;
  'trackInfo.distance': string;
  'trackInfo.distancePlaceholder': string;
  'trackInfo.distanceTooltip': string;
  'trackInfo.eventType': string;
  'trackInfo.eventTypePlaceholder': string;
  'trackInfo.eventTypeTooltip': string;
  'trackInfo.elevation': string;
  'trackInfo.elevationPlaceholder': string;
  'trackInfo.elevationTooltip': string;
  'trackInfo.startDate': string;
  'trackInfo.startDatePlaceholder': string;
  'trackInfo.startDateTooltip': string;
  'trackInfo.startTime': string;
  'trackInfo.startTimePlaceholder': string;
  'trackInfo.startTimeTooltip': string;
  'trackInfo.descriptionAndLinks': string;
  'trackInfo.description': string;
  'trackInfo.descriptionPlaceholder': string;
  'trackInfo.startLocation': string;
  'trackInfo.startLocationPlaceholder': string;
  'trackInfo.payment': string;
  'trackInfo.paymentPlaceholder': string;
  'trackInfo.trackLink': string;
  'trackInfo.trackLinkTooltip': string;
  'trackInfo.trackLinkPlaceholder': string;
  'trackInfo.settings': string;
  'trackInfo.maxParticipants': string;
  'trackInfo.maxParticipantsTooltip': string;
  'trackInfo.registrationOpens': string;
  'trackInfo.registrationOpensPlaceholder': string;
  'trackInfo.registrationOpensTooltip': string;
  'trackInfo.registrationCloses': string;
  'trackInfo.registrationClosesPlaceholder': string;
  'trackInfo.registrationClosesTooltip': string;
  'trackInfo.stripePayment': string;
  'trackInfo.stripePaymentDesc': string;
  'trackInfo.emailConfirmation': string;
  'trackInfo.emailConfirmationDesc': string;

  // Track Builder - Controls Form
  'controls.title': string;
  'controls.generateByDistance': string;
  'controls.addControl': string;
  'controls.controlNumber': string;
  'controls.removeControl': string;
  'controls.controlSite': string;
  'controls.distance': string;
  'controls.distanceTooltip': string;
  'controls.noControlsYet': string;
  'controls.addFirstControl': string;
  'controls.addFirstControlButton': string;

  // Track Builder - Summary
  'summary.preview': string;
  'summary.previewDescription': string;
  'summary.distance': string;
  'summary.elevation': string;
  'summary.startDate': string;
  'summary.startTime': string;
  'summary.lastRegistration': string;
  'summary.startLocation': string;
  'summary.organizer': string;
  'summary.paymentVia': string;
  'summary.other': string;
  'summary.trackLink': string;
  'summary.startList': string;
  'summary.eventGroup': string;
  'summary.selectEventToShow': string;
  'summary.track': string;
  'summary.date': string;
  'summary.minTime': string;
  'summary.maxTime': string;
  'summary.viewOnStrava': string;
  'summary.fillTrackInfoAndAddControls': string;
  'summary.controls': string;
  'summary.control': string;
  'summary.controlsPlural': string;
  'summary.noControlsAddedYet': string;
  'summary.savingTrack': string;
  'summary.complete': string;

  // System Administration - User Admin
  'userAdmin.title': string;
  'userAdmin.description': string;
  'userAdmin.searchUsers': string;
  'userAdmin.newUser': string;
  'userAdmin.refresh': string;
  'userAdmin.name': string;
  'userAdmin.email': string;
  'userAdmin.permissions': string;
  'userAdmin.status': string;
  'userAdmin.actions': string;
  'userAdmin.active': string;
  'userAdmin.inactive': string;
  'userAdmin.edit': string;
  'userAdmin.delete': string;
  'userAdmin.noUsersFound': string;
  'userAdmin.noUsersToShow': string;
  'userAdmin.userInformation': string;
  'userAdmin.firstName': string;
  'userAdmin.firstNameRequired': string;
  'userAdmin.firstNamePlaceholder': string;
  'userAdmin.lastName': string;
  'userAdmin.lastNameRequired': string;
  'userAdmin.lastNamePlaceholder': string;
  'userAdmin.username': string;
  'userAdmin.usernameRequired': string;
  'userAdmin.usernamePlaceholder': string;
  'userAdmin.password': string;
  'userAdmin.passwordPlaceholder': string;
  'userAdmin.generatePassword': string;
  'userAdmin.contactInformation': string;
  'userAdmin.phone': string;
  'userAdmin.phonePlaceholder': string;
  'userAdmin.emailField': string;
  'userAdmin.emailPlaceholder': string;
  'userAdmin.organizer': string;
  'userAdmin.selectOrganizer': string;
  'userAdmin.selectOrganizerOptional': string;
  'userAdmin.organizerDescription': string;
  'userAdmin.superuser': string;
  'userAdmin.superuserDescription': string;
  'userAdmin.admin': string;
  'userAdmin.adminDescription': string;
  'userAdmin.user': string;
  'userAdmin.userDescription': string;
  'userAdmin.volunteer': string;
  'userAdmin.volunteerDescription': string;
  'userAdmin.developer': string;
  'userAdmin.developerDescription': string;

  // System Administration - Club Admin
  'clubAdmin.title': string;
  'clubAdmin.description': string;
  'clubAdmin.searchClubs': string;
  'clubAdmin.newClub': string;
  'clubAdmin.refresh': string;
  'clubAdmin.name': string;
  'clubAdmin.acpCode': string;
  'clubAdmin.actions': string;
  'clubAdmin.officialAcpClub': string;
  'clubAdmin.noClubsFound': string;
  'clubAdmin.noClubsToShow': string;
  'clubAdmin.clubName': string;
  'clubAdmin.clubNameRequired': string;
  'clubAdmin.clubNamePlaceholder': string;
  'clubAdmin.acpCodeField': string;
  'clubAdmin.acpCodePlaceholder': string;
  'clubAdmin.acpCodeDescription': string;

  // System Administration - Site Admin
  'siteAdmin.title': string;
  'siteAdmin.description': string;
  'siteAdmin.searchSites': string;
  'siteAdmin.newSite': string;
  'siteAdmin.refresh': string;
  'siteAdmin.name': string;
  'siteAdmin.location': string;
  'siteAdmin.address': string;
  'siteAdmin.logo': string;
  'siteAdmin.status': string;
  'siteAdmin.actions': string;
  'siteAdmin.active': string;
  'siteAdmin.inactive': string;
  'siteAdmin.edit': string;
  'siteAdmin.delete': string;
  'siteAdmin.noSitesFound': string;
  'siteAdmin.noSitesToShow': string;
  'siteAdmin.clickToViewImage': string;
  'siteAdmin.siteName': string;
  'siteAdmin.siteNameRequired': string;
  'siteAdmin.siteNamePlaceholder': string;
  'siteAdmin.siteLocation': string;
  'siteAdmin.siteLocationPlaceholder': string;
  'siteAdmin.siteAddress': string;
  'siteAdmin.siteAddressPlaceholder': string;
  'siteAdmin.siteDescription': string;
  'siteAdmin.siteDescriptionPlaceholder': string;
  'siteAdmin.siteImage': string;
  'siteAdmin.siteImageDescription': string;

  // System Administration - Organizer Admin
  'organizerAdmin.title': string;
  'organizerAdmin.description': string;
  'organizerAdmin.searchOrganizers': string;
  'organizerAdmin.newOrganizer': string;
  'organizerAdmin.refresh': string;
  'organizerAdmin.organization': string;
  'organizerAdmin.contactPerson': string;
  'organizerAdmin.status': string;
  'organizerAdmin.actions': string;
  'organizerAdmin.active': string;
  'organizerAdmin.inactive': string;
  'organizerAdmin.edit': string;
  'organizerAdmin.delete': string;
  'organizerAdmin.noOrganizersFound': string;
  'organizerAdmin.noOrganizersToShow': string;
  'organizerAdmin.organizationName': string;
  'organizerAdmin.organizationNameRequired': string;
  'organizerAdmin.organizationNamePlaceholder': string;
  'organizerAdmin.contactPersonName': string;
  'organizerAdmin.contactPersonNameRequired': string;
  'organizerAdmin.contactPersonNamePlaceholder': string;
  'organizerAdmin.contactPersonEmail': string;
  'organizerAdmin.contactPersonEmailPlaceholder': string;
  'organizerAdmin.contactPersonPhone': string;
  'organizerAdmin.contactPersonPhonePlaceholder': string;
  'organizerAdmin.website': string;
  'organizerAdmin.websitePlaceholder': string;
  'organizerAdmin.descriptionField': string;
  'organizerAdmin.descriptionPlaceholder': string;

  // Dialog Headers
  'dialog.addSite': string;
  'dialog.addClub': string;
  'dialog.addUser': string;
  'dialog.createOrganizer': string;
  'dialog.editSite': string;
  'dialog.editClub': string;
  'dialog.editUser': string;
  'dialog.editOrganizer': string;

  // Site Dialog Fields
  'siteDialog.place': string;
  'siteDialog.placeRequired': string;
  'siteDialog.placePlaceholder': string;
  'siteDialog.address': string;
  'siteDialog.addressRequired': string;
  'siteDialog.addressPlaceholder': string;
  'siteDialog.latitude': string;
  'siteDialog.latitudeRequired': string;
  'siteDialog.latitudePlaceholder': string;
  'siteDialog.longitude': string;
  'siteDialog.longitudeRequired': string;
  'siteDialog.longitudePlaceholder': string;
  'siteDialog.checkInDistance': string;
  'siteDialog.checkInDistanceRequired': string;
  'siteDialog.checkInDistancePlaceholder': string;
  'siteDialog.description': string;
  'siteDialog.descriptionPlaceholder': string;
  'siteDialog.uploadImage': string;
  'siteDialog.currentImage': string;
  'siteDialog.changeImage': string;

  // Club Dialog Fields
  'clubDialog.clubName': string;
  'clubDialog.clubNameRequired': string;
  'clubDialog.clubNamePlaceholder': string;
  'clubDialog.acpCodeOptional': string;
  'clubDialog.acpCodePlaceholder': string;
  'clubDialog.acpCodeDescription': string;

  // Organizer Dialog Fields
  'organizerDialog.organizationName': string;
  'organizerDialog.organizationNameRequired': string;
  'organizerDialog.organizationNamePlaceholder': string;
  'organizerDialog.contactPersonName': string;
  'organizerDialog.contactPersonNameRequired': string;
  'organizerDialog.contactPersonNamePlaceholder': string;
  'organizerDialog.email': string;
  'organizerDialog.emailRequired': string;
  'organizerDialog.emailPlaceholder': string;
  'organizerDialog.website': string;
  'organizerDialog.websitePlaceholder': string;
  'organizerDialog.paymentWebsite': string;
  'organizerDialog.paymentWebsitePlaceholder': string;
  'organizerDialog.status': string;
  'organizerDialog.active': string;
  'organizerDialog.inactive': string;
  'organizerDialog.logoSvg': string;
  'organizerDialog.logoSvgPlaceholder': string;
  'organizerDialog.description': string;
  'organizerDialog.descriptionPlaceholder': string;
  'organizerDialog.createOrganizer': string;

  // Event Admin
  'eventAdmin.title': string;
  'eventAdmin.description': string;

  // Month Names
  'months.january': string;
  'months.february': string;
  'months.march': string;
  'months.april': string;
  'months.may': string;
  'months.june': string;
  'months.july': string;
  'months.august': string;
  'months.september': string;
  'months.october': string;
  'months.november': string;
  'months.december': string;

  // Volunteer
  'volunteer.title': string;
  'volunteer.description': string;
  'volunteer.helpTooltip': string;
  'volunteer.howItWorks': string;
  'volunteer.toSeeParticipants': string;
  'volunteer.step1': string;
  'volunteer.step2': string;
  'volunteer.step3': string;
  'volunteer.asVolunteer': string;
  'volunteer.checkInParticipants': string;
  'volunteer.markDNF': string;
  'volunteer.undoActions': string;
  'volunteer.event': string;
  'volunteer.checkpoint': string;
  'volunteer.started': string;
  'volunteer.checkedIn': string;
  'volunteer.checkedOut': string;
  'volunteer.atCheckpoint': string;
  'volunteer.expected': string;
  'volunteer.manageParticipants': string;
  'volunteer.selectEvent': string;
  'volunteer.selectTrack': string;
  'volunteer.selectCheckpoint': string;
  'volunteer.searchParticipants': string;
  'volunteer.selectedCheckpoint': string;
  'volunteer.startNumber': string;
  'volunteer.name': string;
  'volunteer.lastNameFirstName': string;
  'volunteer.time': string;
  'volunteer.passed': string;
  'volunteer.status': string;
  'volunteer.checkInOut': string;
  'volunteer.actions': string;
  'volunteer.volunteer': string;
  'volunteer.undoCheckIn': string;
  'volunteer.checkIn': string;
  'volunteer.undoCheckOut': string;
  'volunteer.checkOut': string;
  'volunteer.undoDNF': string;
  'volunteer.dnf': string;
  'volunteer.noParticipantsFound': string;
  'volunteer.selectCheckpointToSee': string;
  'volunteer.showingResults': string;
  'volunteer.totalParticipants': string;
  'volunteer.remaining': string;
  'volunteer.expectedPercentage': string;

  // MSR
  'msr.title': string;
  'msr.overview': string;
  'msr.participants': string;
  'msr.other': string;
  'msr.description': string;
  'msr.participantsDescription': string;
  'msr.otherDescription': string;
  'msr.selectEvent': string;
  'msr.loadingEvents': string;
  'msr.loadingStats': string;
  'msr.loadingParticipants': string;
  'msr.loadingOptionals': string;
  'msr.noEventsFound': string;
  'msr.noStatsFound': string;
  'msr.noParticipantsFound': string;
  'msr.noOptionalsFound': string;
  'msr.errorLoadingEvents': string;
  'msr.errorLoadingStats': string;
  'msr.errorLoadingParticipants': string;
  'msr.errorLoadingOptionals': string;
  'msr.totalRegistrations': string;
  'msr.confirmedRegistrations': string;
  'msr.totalReservations': string;
  'msr.maxRegistrations': string;
  'msr.registrationPercentage': string;
  'msr.optionalProducts': string;
  'msr.registrationTrends': string;
  'msr.last7Days': string;
  'msr.last30Days': string;
  'msr.searchPlaceholder': string;
  'msr.searchParticipantsPlaceholder': string;
  'msr.searchOptionalsPlaceholder': string;
  'msr.filterByStatus': string;
  'msr.filterByProduct': string;
  'msr.filterByDate': string;
  'msr.allStatuses': string;
  'msr.allProducts': string;
  'msr.confirmed': string;
  'msr.reservation': string;
  'msr.startDate': string;
  'msr.endDate': string;
  'msr.filterType': string;
  'msr.filterByEvent': string;
  'msr.activeFilters': string;
  'msr.productNotFound': string;
  'msr.exportCsv': string;
  'msr.refresh': string;
  'msr.loadStats': string;
  'msr.loadParticipants': string;
  'msr.loadOptionals': string;
  'msr.showingResults': string;
  'msr.of': string;
  'msr.products': string;
  'msr.filtered': string;
  'msr.registrations': string;
  'msr.noResultsMatchFilters': string;
  'msr.csvExportTooltip': string;
  'msr.csvExportNoData': string;
  'msr.spots': string;
  'msr.name': string;
  'msr.email': string;
  'msr.quantity': string;
  'msr.additionalInfo': string;
  'msr.registrationDate': string;
  'msr.event': string;
  'msr.status': string;
  'msr.product': string;
  'msr.exportedFrom': string;
  'msr.exportedAt': string;
  'msr.numberOfParticipants': string;
  'msr.numberOfRegistrations': string;
  'msr.numberOfProducts': string;
}

@Injectable({
  providedIn: 'root'
})
export class TranslationService {
  private languageService = inject(LanguageService);

  private translations: Record<SupportedLanguage, TranslationKeys> = {
    sv: {
      // Common
      'common.loading': 'Laddar...',
      'common.error': 'Fel',
      'common.success': 'Framgång',
      'common.cancel': 'Avbryt',
      'common.map': 'Karta',
      'common.confirm': 'Bekräfta',
      'common.summary': 'Sammanfattning',
      'common.yes': 'Ja',
      'common.no': 'Nej',
      'common.save': 'Spara',
      'common.edit': 'Redigera',
      'common.delete': 'Ta bort',
      'common.add': 'Lägg till',
      'common.close': 'Stäng',
      'common.back': 'Tillbaka',
      'common.next': 'Nästa',
      'common.previous': 'Föregående',
      'common.search': 'Sök',
      'common.filter': 'Filtrera',
      'common.sort': 'Sortera',
      'common.refresh': 'Uppdatera',
      'common.export': 'Exportera',
      'common.import': 'Importera',
      'common.print': 'Skriv ut',
      'common.download': 'Ladda ner',
      'common.upload': 'Ladda upp',
      'common.update': 'Uppdatera',
      'common.create': 'Skapa',
      'common.view': 'Visa',
      'common.details': 'Detaljer',
      'common.actions': 'Åtgärder',
      'common.select': 'Välj',
      'common.clear': 'Rensa',
      'common.reset': 'Återställ',
      'common.submit': 'Skicka',
      'common.continue': 'Fortsätt',
      'common.finish': 'Slutför',
      'common.start': 'Starta',
      'common.stop': 'Stoppa',
      'common.pause': 'Pausa',
      'common.resume': 'Återuppta',
      'common.retry': 'Försök igen',
      'common.undo': 'Ångra',
      'common.redo': 'Gör om',
      'common.copy': 'Kopiera',
      'common.paste': 'Klistra in',
      'common.cut': 'Klipp ut',
      'common.selectAll': 'Välj alla',
      'common.none': 'Ingen',
      'common.all': 'Alla',
      'common.optional': 'Valfri',
      'common.required': 'Obligatorisk',
      'common.and': 'och',
      'common.to': 'för att',

      // Navigation
      'nav.dashboard': 'Översikt',
      'nav.tracks': 'Banor',
      'nav.trackList': 'Banalista',
      'nav.participants': 'Deltagare',
      'nav.events': 'Evenemang',
      'nav.reports': 'Rapportera',
      'nav.clubs': 'Klubbar',
      'nav.checkpoints': 'Kontrollplatser',
      'nav.users': 'Användare',
      'nav.organizers': 'Arrangörer',
      'nav.settings': 'Inställningar',
      'nav.profile': 'Profil',
      'nav.logout': 'Logga ut',
      'nav.home': 'Hem',
      'nav.admin': 'Admin',
      'nav.volunteer': 'Volontär',
      'nav.competitor': 'Tävlande',
      'nav.participantList': 'Deltagarlista',
      'nav.uploadParticipants': 'Ladda upp deltagare',
      'nav.createNewEvent': 'Skapa nytt arrangemang',
      'nav.gpxImport': 'GPX Import',
      'nav.copyTrack': 'Kopiera bana',
      'nav.reportToAcp': 'Rapport till ACP',
      'nav.system': 'System',

      // Login
      'login.title': 'Digitalt Brevet-kort med GPS-validering',
      'login.username': 'Användarnamn',
      'login.password': 'Lösenord',
      'login.loginButton': 'Logga in',
      'login.forgotPassword': 'Glömt lösenord?',
      'login.rememberMe': 'Kom ihåg mig',
      'login.invalidCredentials': 'Ogiltigt användarnamn eller lösenord',
      'login.loginSuccess': 'Inloggning lyckades',
      'login.loginError': 'Inloggning misslyckades',
      'login.competitorNotAllowed': 'Tävlande ska använda tävlande-appen',

      // Admin
      'admin.dashboard': 'Admin Dashboard',
      'admin.overview': 'Översikt',
      'admin.statistics': 'Statistik',
      'admin.management': 'Hantering',
      'admin.settings': 'Admin-inställningar',
      'admin.users': 'Användarhantering',
      'admin.permissions': 'Behörigheter',
      'admin.roles': 'Roller',
      'admin.audit': 'Granskningslogg',

      // Competitor
      'competitor.dashboard': 'Tävlande Dashboard',
      'competitor.myTracks': 'Mina banor',
      'competitor.myResults': 'Mina resultat',
      'competitor.profile': 'Min profil',
      'competitor.registration': 'Registrering',
      'competitor.trackSelection': 'Bansval',
      'competitor.payment': 'Betalning',
      'competitor.confirmation': 'Bekräftelse',

      // Volunteer
      'volunteer.dashboard': 'Volontär Dashboard',
      'volunteer.checkpoints': 'Kontrollplatser',
      'volunteer.participants': 'Deltagare',
      'volunteer.tracking': 'Spårning',
      'volunteer.reports': 'Rapporter',
      'volunteer.schedule': 'Schema',
      'volunteer.tasks': 'Uppgifter',

      // Forms
      'form.name': 'Namn',
      'form.email': 'E-post',
      'form.phone': 'Telefon',
      'form.address': 'Adress',
      'form.city': 'Stad',
      'form.postalCode': 'Postnummer',
      'form.country': 'Land',
      'form.date': 'Datum',
      'form.time': 'Tid',
      'form.dateTime': 'Datum & Tid',
      'form.startDate': 'Startdatum',
      'form.endDate': 'Slutdatum',
      'form.duration': 'Varaktighet',
      'form.distance': 'Distans',
      'form.elevation': 'Höjdskillnad',
      'form.difficulty': 'Svårighetsgrad',
      'form.description': 'Beskrivning',
      'form.notes': 'Anteckningar',
      'form.status': 'Status',
      'form.type': 'Typ',
      'form.category': 'Kategori',
      'form.priority': 'Prioritet',
      'form.tags': 'Taggar',
      'form.file': 'Fil',
      'form.image': 'Bild',
      'form.document': 'Dokument',
      'form.clubName': 'Klubbnamn',
      'form.enterClubName': 'Ange klubbnamn',
      'form.acpCodeOptional': 'ACP-kod (Valfritt)',
      'form.leaveEmptyIfNoAcpCode': 'Lämna tomt om ingen ACP-kod',
      'form.leaveEmptyIfNoAcpCodeDescription': 'Lämna detta fält tomt om din klubb inte har en ACP-kod',

      // Validation
      'validation.required': 'Detta fält är obligatoriskt',
      'validation.email': 'Ange en giltig e-postadress',
      'validation.phone': 'Ange ett giltigt telefonnummer',
      'validation.minLength': 'Minsta längd är {0} tecken',
      'validation.maxLength': 'Maximal längd är {0} tecken',
      'validation.min': 'Minsta värde är {0}',
      'validation.max': 'Maximalt värde är {0}',
      'validation.pattern': 'Ange ett giltigt format',
      'validation.confirmPassword': 'Lösenorden matchar inte',
      'validation.unique': 'Detta värde finns redan',
      'validation.date': 'Ange ett giltigt datum',
      'validation.time': 'Ange en giltig tid',
      'validation.futureDate': 'Datumet måste vara i framtiden',
      'validation.pastDate': 'Datumet måste vara i det förflutna',
      'validation.clubNameRequired': 'Klubbnamn krävs',

      // Messages
      'message.success': 'Framgång',
      'message.error': 'Fel',
      'message.warning': 'Varning',
      'message.info': 'Information',
      'message.confirm': 'Är du säker?',
      'message.unsavedChanges': 'Du har osparade ändringar. Är du säker på att du vill lämna?',
      'message.dataSaved': 'Data sparades framgångsrikt',
      'message.dataUpdated': 'Data uppdaterades framgångsrikt',
      'message.dataDeleted': 'Data togs bort framgångsrikt',
      'message.dataLoaded': 'Data laddades framgångsrikt',
      'message.operationFailed': 'Åtgärden misslyckades',
      'message.networkError': 'Nätverksfel uppstod',
      'message.serverError': 'Serverfel uppstod',
      'message.unauthorized': 'Obehörig åtkomst',
      'message.forbidden': 'Åtkomst förbjuden',
      'message.notFound': 'Resurs hittades inte',
      'message.timeout': 'Förfrågan timeout',
      'message.retry': 'Försök igen',

      // Status
      'status.active': 'Aktiv',
      'status.inactive': 'Inaktiv',
      'status.pending': 'Väntar',
      'status.approved': 'Godkänd',
      'status.rejected': 'Avvisad',
      'status.completed': 'Slutförd',
      'status.cancelled': 'Inställd',
      'status.draft': 'Utkast',
      'status.published': 'Publicerad',
      'status.archived': 'Arkiverad',
      'status.deleted': 'Borttagen',
      'status.enabled': 'Aktiverad',
      'status.disabled': 'Inaktiverad',
      'status.available': 'Tillgänglig',
      'status.unavailable': 'Otillgänglig',
      'status.online': 'Online',
      'status.offline': 'Offline',
      'status.connected': 'Ansluten',
      'status.disconnected': 'Frånkopplad',
      'status.synced': 'Synkroniserad',
      'status.syncing': 'Synkroniserar',
      'status.started': 'Startad',
      'status.stopped': 'Stoppad',
      'status.paused': 'Pausad',
      'status.resumed': 'Återupptagen',

      // Coming Soon
      'comingSoon.gpxImport': 'GPX Import',
      'comingSoon.copyTrack': 'Kopiera spår',
      'comingSoon.badge': 'Kommer snart',

      // Dashboard/Overview
      'dashboard.welcome': 'Välkommen till eBrevet',
      'dashboard.welcomeBack': 'Välkommen tillbaka! Här är din översikt',
      'dashboard.overview': 'Översikt',
      'dashboard.overviewAndStats': 'Översikt och statistik',
      'dashboard.totalParticipants': 'Totalt deltagare',
      'dashboard.activeTracks': 'Aktiva banor',
      'dashboard.activeEvents': 'Aktiva evenemang',
      'dashboard.completed': 'Slutförda',
      'dashboard.registered': 'Registrerade',
      'dashboard.dns': 'DNS',
      'dashboard.latestRegistration': 'Senaste registrering',
      'dashboard.topPerformingTracks': 'Toppresterande banor',
      'dashboard.mostPopularTracks': 'Mest populära banor (Senaste 30 dagarna)',
      'dashboard.todaysTracks': 'Dagens banor',
      'dashboard.tracksRunningToday': 'Banor som körs idag',
      'dashboard.last7Days': 'Senaste 7 dagarna',
      'dashboard.today': 'Idag',
      'dashboard.thisYear': 'Detta år',
      'dashboard.total': 'Totalt',
      'dashboard.tracks': 'banor',
      'dashboard.events': 'evenemang',
      'dashboard.participants': 'deltagare',
      'dashboard.addNewTrack': 'Lägg till ny bana',
      'dashboard.activeEventsRunning': 'Aktiva evenemang körs',
      'dashboard.didNotStart': 'Startade inte',
      'dashboard.totalRegistered': 'Totalt registrerade',
      'dashboard.newRegistration': 'Ny registrering',
      'dashboard.noRegistrationsYet': 'Inga registreringar än',
      'dashboard.noTracksLast30Days': 'Inga banor med deltagare de senaste 30 dagarna',
      'dashboard.noTracksRunningToday': 'Inga banor körs idag',
      'dashboard.heightDifference': 'm höjdskillnad',
      'dashboard.developedBy': 'Utvecklad av',
      'dashboard.dailyParticipants': 'Dagens deltagare',
      'dashboard.startedToday': 'Startade idag',
      'dashboard.brokeToday': 'Bröt idag',
      'dashboard.didNotStartToday': 'Startade inte idag',
      'dashboard.weekly': 'Veckovis',
      'dashboard.eventGroup': 'Arrangemangsgrupp',
      'dashboard.manageEventGroups': 'Hantera arrangemangsgrupper',
      'dashboard.manageTracks': 'Hantera banor',
      'dashboard.manageParticipants': 'Hantera deltagare',
      'dashboard.manageClubs': 'Hantera klubbar',
      'dashboard.managePlaces': 'Hantera platser',
      'dashboard.manageCheckpoints': 'Hantera kontrollplatser',
      'dashboard.manageUsers': 'Hantera användare',
      'dashboard.manageOrganizers': 'Hantera arrangörer',

      // Participant Management
      'participant.manageParticipants': 'Hantera Deltagare',
      'participant.manageParticipantsDescription': 'Hantera deltagare, registreringar och resultat för evenemang',
      'participant.participantList': 'Deltagarlista',
      'participant.participantListDescription': 'Visa och hantera alla deltagare',
      'participant.upload': 'Ladda upp',
      'participant.uploadDescription': 'Importera deltagare från fil',
      'participant.searchParticipants': 'Sök deltagare...',
      'participant.exportStartList': 'Exportera startlista',
      'participant.exportHomologation': 'Exportera homologation',
      'participant.publishResults': 'Publicera resultat',
      'participant.unpublishResults': 'Ångra publicera',
      'participant.selectTrackFirst': 'Välj en bana först',
      'participant.trackMustBePublished': 'Banan måste vara publicerad först',
      'participant.moveAllParticipants': 'Flytta Alla Deltagare',
      'participant.startNumber': 'Startnummer',
      'participant.lastName': 'Efternamn',
      'participant.firstName': 'Förnamn',
      'participant.club': 'Klubb',
      'participant.actions': 'Åtgärder',
      'participant.move': 'Flytta',
      'participant.moveTooltip': 'Flytta deltagare till annan bana',
      'participant.edit': 'Redigera',
      'participant.delete': 'Ta bort',
      'participant.address': 'Adress',
      'participant.city': 'Ort',
      'participant.country': 'Land',
      'participant.phone': 'Telefon',
      'participant.email': 'Email',
      'participant.finishTime': 'Sluttid',
      'participant.homologationNumber': 'Homolg.nr',
      'participant.noParticipantsFound': 'Inga deltagare hittades',
      'participant.showingParticipants': 'Visar {first} till {last} av {totalRecords} deltagare',

      // Checkpoint Table
      'checkpoint.logo': 'Logga',
      'checkpoint.address': 'Adress',
      'checkpoint.city': 'Ort',
      'checkpoint.distance': 'Distans',
      'checkpoint.checkins': 'Incheckningar',
      'checkpoint.checkouts': 'Utcheckningar',
      'checkpoint.action': 'Åtgärd',
      'checkpoint.changeTime': 'Ändra tid',
      'checkpoint.undo': 'Ångra',
      'checkpoint.checkin': 'Checka in',
      'checkpoint.checkout': 'Checka ut',
      'checkpoint.undoCheckout': 'Ångra checkout',
      'checkpoint.selectTrackToSeeCheckpoints': 'Välj en bana för att se kontrollpunkter',

      // Edit Checkpoint Time Dialog
      'checkpointDialog.changeCheckinTime': 'Ändra incheckningstid',
      'checkpointDialog.changeCheckoutTime': 'Ändra utcheckningtid',
      'checkpointDialog.enterDateAndTime': 'Ange datum och tid',
      'checkpointDialog.cancel': 'Avbryt',
      'checkpointDialog.save': 'Spara',

      // Upload Participants
      'upload.uploadParticipants': 'Ladda upp deltagare',
      'upload.uploadParticipantsDescription': 'Här kan du ladda upp deltagare med en CSV-fil',
      'upload.csvFormatForParticipants': 'CSV-format för deltagare',
      'upload.sixteenColumnsSeparatedBySemicolon': '16 kolumner separerade med semikolon (;)',
      'upload.startNumber': 'Startnummer',
      'upload.firstName': 'Förnamn',
      'upload.lastName': 'Efternamn',
      'upload.gender': 'Kön (Man/Kvinna)',
      'upload.clubName': 'Klubbnamn',
      'upload.address': 'Adress',
      'upload.postalCode': 'Postnummer',
      'upload.city': 'Stad',
      'upload.country': 'Land',
      'upload.email': 'E-post',
      'upload.phone': 'Telefon',
      'upload.registrationDate': 'Registreringsdatum (YYYY-MM-DD HH:MM:SS)',
      'upload.birthYear': 'Födelseår (YYYY)',
      'upload.referenceNumber': 'Referensnummer',
      'upload.physicalBrevetCard': 'Fysisk brevetkort (Ja/Nej eller 1/0)',
      'upload.additionalInformation': 'Ytterligare information',
      'upload.example': 'Exempel:',
      'upload.trackActive': 'Bana aktiv',
      'upload.trackInactive': 'Bana inaktiv',
      'upload.uploadNotAllowedForInactiveTracks': 'Upload av deltagare är inte tillåtet för inaktiva banor.',
      'upload.chooseCsv': 'Välj csv',
      'upload.uploadCsv': 'Ladda upp csv',
      'upload.selectedFiles': 'Valda filer',
      'upload.uploadedFile': 'Uppladdad fil',
      'upload.uploadResult': 'Upload Resultat',
      'upload.totalRows': 'Totalt rader',
      'upload.successful': 'Framgångsrika',
      'upload.skipped': 'Hoppade över',
      'upload.failed': 'Misslyckade',
      'upload.detailedErrors': 'Detaljerade fel:',
      'upload.row': 'Rad',
      'upload.data': 'Data',
      'upload.createdParticipants': 'Skapade deltagare:',
      'upload.participant': 'Deltagare',
      'upload.uploadComplete': 'Upload Complete',
      'upload.uploadIssues': 'Upload Issues',
      'upload.uploadFailed': 'Upload Failed',

      // Track Management
      'track.manageTracks': 'Hantera Banor',
      'track.manageTracksDescription': 'Hantera banor, kontrollpunkter och evenemang',
      'track.trackList': 'Banlista',
      'track.trackListDescription': 'Hantera och visa alla banor',
      'track.trackBuilder': 'Banbyggare',
      'track.trackBuilderDescription': 'Skapa och redigera banor',
      'track.openTrackBuilder': 'Öppna banbyggare',
      'track.searchTracks': 'Sök banor...',
      'track.event': 'Event',
      'track.startDate': 'Startdatum',
      'track.start': 'Start',
      'track.endDate': 'Slutdatum',
      'track.end': 'Slut',
      'track.status': 'Status',
      'track.trackingLink': 'Länk till trackingsida',
      'track.tracking': 'Tracking',
      'track.action': 'Åtgärd',
      'track.completed': 'Genomförd',
      'track.ongoing': 'Pågående',
      'track.cancelled': 'Inställd',
      'track.followParticipantsOnEvent': 'Följ deltagare på event',
      'track.remove': 'Ta bort',
      'track.noTracksFound': 'Inga banor hittades',
      'track.noTracksToShow': 'Det finns inga banor att visa.',
      'track.track': 'Bana',
      'track.trackLink': 'Länk till bana',
      'track.date': 'Datum',
      'track.distance': 'Distans',
      'track.cyclistView': 'Cyklistens vy',
      'track.trackingPage': 'Trackingsida',
      'track.followParticipantsOnTrack': 'Följ deltagare på bana',
      'track.noTracksOnEvent': 'Det finns inga banor på det här eventet.',
      'track.controls': 'Kontroller',

      // Checkpoint Table (General)
      'checkpointTable.logo': 'Logga',
      'checkpointTable.address': 'Adress',
      'checkpointTable.city': 'Ort',
      'checkpointTable.distance': 'Distans',
      'checkpointTable.opens': 'Öppnar',
      'checkpointTable.closes': 'Stänger',
      'checkpointTable.noCheckpointsFound': 'Inga kontrollplatser hittades',
      'checkpointTable.noCheckpointsOnTrack': 'Det finns inga kontrollplatser på den här banan.',

      // Event Creation
      'event.createEvent': 'Skapa nytt arrangemang',
      'event.addEvent': 'Lägg till Evenemang',
      'event.title': 'Titel',
      'event.titleRequired': 'Titel krävs',
      'event.titlePlaceholder': 'Ex: Månskensbrevet',
      'event.startDate': 'Startdatum',
      'event.endDate': 'Slutdatum',
      'event.status': 'Status',
      'event.description': 'Beskrivning',
      'event.descriptionPlaceholder': 'Ange evenemangsbeskrivning...',
      'event.statusActive': 'Aktiv',
      'event.statusCancelled': 'Inställd',
      'event.statusCompleted': 'Utförd',
      'event.cancel': 'Avbryt',
      'event.save': 'Spara',
      'event.newEvent': 'Nytt Evenemang',
      'event.searchEvents': 'Sök Arrangemangsgrupper...',
      'event.refresh': 'Uppdatera',
      'event.name': 'NAMN',
      'event.actions': 'ÅTGÄRDER',
      'event.edit': 'Redigera',
      'event.delete': 'Ta bort',
      'event.noEventsFound': 'Inga Evenemang Hittades',
      'event.noEventsToShow': 'Det finns inga evenemang att visa.',
      'event.completed': 'Genomförd',
      'event.ongoing': 'Pågående',
      'event.cancelled': 'Inställd',

      // Track Builder - Event Creation
      'trackBuilder.createNewEvent': 'Skapa nytt arrangemang',
      'trackBuilder.createNewEventDescription': 'Skapa ett helt nytt arrangemang i loppservice med kalenderpost',
      'trackBuilder.buildFromGpx': 'Bygg från GPX-fil',
      'trackBuilder.buildFromGpxDescription': 'Importera en GPX-fil för att automatiskt skapa bana och kontroller',
      'trackBuilder.copyExistingTrack': 'Kopiera befintlig bana',
      'trackBuilder.copyExistingTrackDescription': 'Skapa en ny bana baserad på en befintlig bana',
      'trackBuilder.upcomingFeature': 'Kommande funktion',
      'trackBuilder.fillInformation': 'Skapa nytt arrangemang',
      'trackBuilder.fillInformationDescription': 'Fyll i informationen nedan för att skapa ditt arrangemang och bana',

      // Track Builder - Track Info Form
      'trackInfo.eventGroup': 'Arrangemangsgrupp',
      'trackInfo.selectEventGroup': 'Välj arrangemangsgrupp',
      'trackInfo.organizer': 'Arrangör',
      'trackInfo.selectOrganizer': 'Välj arrangör',
      'trackInfo.basicInformation': 'Grundläggande information',
      'trackInfo.eventName': 'Arrangemangsnnam',
      'trackInfo.eventNameTooltip': 'Banans namn tex MSR 1200',
      'trackInfo.eventNamePlaceholder': 't.ex. BRM 300 Bönhamn',
      'trackInfo.distance': 'Distans (km)',
      'trackInfo.distancePlaceholder': 'Välj eller skriv egen distans',
      'trackInfo.distanceTooltip': 'Välj en standarddistans eller skriv egen',
      'trackInfo.eventType': 'Typ av arrangemang',
      'trackInfo.eventTypePlaceholder': 'Välj typ av arrangemang',
      'trackInfo.eventTypeTooltip': 'Välj typ av arrangemang',
      'trackInfo.elevation': 'Höjdskillnad (m)',
      'trackInfo.elevationPlaceholder': '1700',
      'trackInfo.elevationTooltip': 'Total höjdskillnad i meter',
      'trackInfo.startDate': 'Startdatum',
      'trackInfo.startDatePlaceholder': 'YYYY-MM-DD',
      'trackInfo.startDateTooltip': 'Om fältet lämnas tomt sätt startdatum automatiskt till nästkommande dag',
      'trackInfo.startTime': 'Starttid',
      'trackInfo.startTimePlaceholder': '07:00',
      'trackInfo.startTimeTooltip': '24-timmarsformat, t.ex. 07:00',
      'trackInfo.descriptionAndLinks': 'Beskrivning och länkar',
      'trackInfo.description': 'Beskrivning',
      'trackInfo.descriptionPlaceholder': 'Beskriv arrangemanget...',
      'trackInfo.startLocation': 'Startplats',
      'trackInfo.startLocationPlaceholder': 't.ex. ICA Kvantum, Norrköping',
      'trackInfo.payment': 'Betalning',
      'trackInfo.paymentPlaceholder': 't.ex. Swish 123 456 78 90',
      'trackInfo.trackLink': 'Länk till banan',
      'trackInfo.trackLinkTooltip': 'Länk till banan på Strava',
      'trackInfo.trackLinkPlaceholder': 'https://www.ridewithgps.com/routes/...',
      'trackInfo.settings': 'Inställningar',
      'trackInfo.maxParticipants': 'Max deltagare',
      'trackInfo.maxParticipantsTooltip': 'Maximalt antal deltagare',
      'trackInfo.registrationOpens': 'Anmälan öppnar',
      'trackInfo.registrationOpensPlaceholder': 'YYYY-MM-DD HH:MM',
      'trackInfo.registrationOpensTooltip': 'Datum och tid när anmälan öppnar',
      'trackInfo.registrationCloses': 'Anmälan stänger',
      'trackInfo.registrationClosesPlaceholder': 'YYYY-MM-DD HH:MM',
      'trackInfo.registrationClosesTooltip': 'Datum och tid när anmälan stänger',
      'trackInfo.stripePayment': 'Stripe kortbetalning',
      'trackInfo.stripePaymentDesc': 'Aktivera säker kortbetalning',
      'trackInfo.emailConfirmation': 'E-postbekräftelse',
      'trackInfo.emailConfirmationDesc': 'Skicka bekräftelse vid anmälan',

      // Track Builder - Controls Form
      'controls.title': 'Kontroller',
      'controls.generateByDistance': 'Generera efter distans',
      'controls.addControl': 'Lägg till kontroll',
      'controls.controlNumber': 'Kontroll #',
      'controls.removeControl': 'Ta bort kontroll',
      'controls.controlSite': 'Kontrollplats',
      'controls.distance': 'Distans (km)',
      'controls.distanceTooltip': 'Distans till kontrollpunkt i kilometer',
      'controls.noControlsYet': 'Inga kontroller ännu',
      'controls.addFirstControl': 'Lägg till din första kontroll för att komma igång',
      'controls.addFirstControlButton': 'Lägg till första kontrollen',

      // Track Builder - Summary
      'summary.preview': 'Förhandsvisning - Så här kommer eventet att visas',
      'summary.previewDescription': 'Förhandsvisning - Så här kommer eventet att visas',
      'summary.distance': 'Distans:',
      'summary.elevation': 'Höjdmeter:',
      'summary.startDate': 'Startdatum:',
      'summary.startTime': 'Starttid:',
      'summary.lastRegistration': 'Sista anmälan:',
      'summary.startLocation': 'Startort:',
      'summary.organizer': 'Arrangör:',
      'summary.paymentVia': 'Betala via:',
      'summary.other': 'Övrigt:',
      'summary.trackLink': 'Länk till bana',
      'summary.startList': 'Startlista',
      'summary.eventGroup': 'Arrangemangsgrupp',
      'summary.selectEventToShow': 'Välj ett arrangemang för att visa information här',
      'summary.track': 'Bana',
      'summary.date': 'Datum',
      'summary.minTime': 'Min tid:',
      'summary.maxTime': 'Max tid:',
      'summary.viewOnStrava': 'Visa på Strava',
      'summary.fillTrackInfoAndAddControls': 'Fyll i banans information och lägg till kontroller för att beräkna banan',
      'summary.controls': 'Kontroller',
      'summary.control': 'kontroll',
      'summary.controlsPlural': 'kontroller',
      'summary.noControlsAddedYet': 'Inga kontroller tillagda än',
      'summary.savingTrack': 'Sparar bana...',
      'summary.complete': 'Slutför',

      // System Administration - User Admin
      'userAdmin.title': 'Användarhantering',
      'userAdmin.description': 'Hantera och organisera alla användare',
      'userAdmin.searchUsers': 'Sök användare...',
      'userAdmin.newUser': 'Ny Användare',
      'userAdmin.refresh': 'Uppdatera',
      'userAdmin.name': 'NAMN',
      'userAdmin.email': 'E-POST',
      'userAdmin.permissions': 'BEHÖRIGHETER',
      'userAdmin.status': 'STATUS',
      'userAdmin.actions': 'ÅTGÄRDER',
      'userAdmin.active': 'Aktiv',
      'userAdmin.inactive': 'Inaktiv',
      'userAdmin.edit': 'Redigera',
      'userAdmin.delete': 'Ta bort',
      'userAdmin.noUsersFound': 'Inga Användare Hittades',
      'userAdmin.noUsersToShow': 'Det finns inga användare att visa.',
      'userAdmin.userInformation': 'Användaruppgifter',
      'userAdmin.firstName': 'Förnamn',
      'userAdmin.firstNameRequired': 'Förnamn krävs',
      'userAdmin.firstNamePlaceholder': 'Ange förnamn',
      'userAdmin.lastName': 'Efternamn',
      'userAdmin.lastNameRequired': 'Efternamn krävs',
      'userAdmin.lastNamePlaceholder': 'Ange efternamn',
      'userAdmin.username': 'Användarnamn',
      'userAdmin.usernameRequired': 'Användarnamn krävs',
      'userAdmin.usernamePlaceholder': 'Ange användarnamn',
      'userAdmin.password': 'Lösenord',
      'userAdmin.passwordPlaceholder': 'Ange eller generera lösenord',
      'userAdmin.generatePassword': 'Generera lösenord',
      'userAdmin.contactInformation': 'Kontaktuppgifter',
      'userAdmin.phone': 'Telefon',
      'userAdmin.phonePlaceholder': 'Ange telefonnummer',
      'userAdmin.emailField': 'E-post',
      'userAdmin.emailPlaceholder': 'Ange e-postadress',
      'userAdmin.organizer': 'Arrangör',
      'userAdmin.selectOrganizer': 'Välj arrangör',
      'userAdmin.selectOrganizerOptional': 'Välj arrangör (valfritt)',
      'userAdmin.organizerDescription': 'Välj en arrangör som användaren ska kopplas till (valfritt)',
      'userAdmin.superuser': 'Superanvändare',
      'userAdmin.superuserDescription': 'Superanvändare med alla behörigheter',
      'userAdmin.admin': 'Administratör',
      'userAdmin.adminDescription': 'Administratör med skriv och läsrättigheter',
      'userAdmin.user': 'Användare',
      'userAdmin.userDescription': 'Läsbehörighet och viss skrivbehörighet',
      'userAdmin.volunteer': 'Volontär',
      'userAdmin.volunteerDescription': 'Behörighet att checka in och se passeringar vid kontroller',
      'userAdmin.developer': 'Utvecklare',
      'userAdmin.developerDescription': 'Specialbehörighet för utvecklare',

      // System Administration - Club Admin
      'clubAdmin.title': 'Hantera Klubbar',
      'clubAdmin.description': 'Hantera och organisera alla klubbar',
      'clubAdmin.searchClubs': 'Sök klubbar...',
      'clubAdmin.newClub': 'Ny Klubb',
      'clubAdmin.refresh': 'Uppdatera',
      'clubAdmin.name': 'NAMN',
      'clubAdmin.acpCode': 'ACP KOD',
      'clubAdmin.actions': 'ÅTGÄRDER',
      'clubAdmin.officialAcpClub': 'Officiell ACP-klubb',
      'clubAdmin.noClubsFound': 'Inga Klubbar Hittades',
      'clubAdmin.noClubsToShow': 'Det finns inga klubbar att visa.',
      'clubAdmin.clubName': 'Klubbnamn',
      'clubAdmin.clubNameRequired': 'Klubbnamn krävs',
      'clubAdmin.clubNamePlaceholder': 'Ange klubbnamn',
      'clubAdmin.acpCodeField': 'ACP-kod',
      'clubAdmin.acpCodePlaceholder': 'Ange ACP-kod',
      'clubAdmin.acpCodeDescription': 'Officiell ACP-kod för klubben',

      // System Administration - Site Admin
      'siteAdmin.title': 'Hantera Kontrollpunkter',
      'siteAdmin.description': 'Hantera och organisera alla kontrollpunkter',
      'siteAdmin.searchSites': 'Sök platser...',
      'siteAdmin.newSite': 'Ny Plats',
      'siteAdmin.refresh': 'Uppdatera',
      'siteAdmin.name': 'NAMN',
      'siteAdmin.location': 'PLATS',
      'siteAdmin.address': 'ADRESS',
      'siteAdmin.logo': 'LOGGA',
      'siteAdmin.status': 'STATUS',
      'siteAdmin.actions': 'ÅTGÄRDER',
      'siteAdmin.active': 'Aktiv',
      'siteAdmin.inactive': 'Inaktiv',
      'siteAdmin.edit': 'Redigera',
      'siteAdmin.delete': 'Ta bort',
      'siteAdmin.noSitesFound': 'Inga Platser Hittades',
      'siteAdmin.noSitesToShow': 'Det finns inga platser att visa.',
      'siteAdmin.clickToViewImage': 'Click to view image',
      'siteAdmin.siteName': 'Platsnamn',
      'siteAdmin.siteNameRequired': 'Platsnamn krävs',
      'siteAdmin.siteNamePlaceholder': 'Ange platsnamn',
      'siteAdmin.siteLocation': 'Plats',
      'siteAdmin.siteLocationPlaceholder': 'Ange plats',
      'siteAdmin.siteAddress': 'Adress',
      'siteAdmin.siteAddressPlaceholder': 'Ange adress',
      'siteAdmin.siteDescription': 'Beskrivning',
      'siteAdmin.siteDescriptionPlaceholder': 'Ange beskrivning',
      'siteAdmin.siteImage': 'Bild',
      'siteAdmin.siteImageDescription': 'Ladda upp bild för platsen',

      // System Administration - Organizer Admin
      'organizerAdmin.title': 'Hantera Arrangörer',
      'organizerAdmin.description': 'Hantera och organisera alla arrangörer',
      'organizerAdmin.searchOrganizers': 'Sök organisatörer...',
      'organizerAdmin.newOrganizer': 'Ny Organisatör',
      'organizerAdmin.refresh': 'Uppdatera',
      'organizerAdmin.organization': 'ORGANISATION',
      'organizerAdmin.contactPerson': 'KONTAKTPERSON',
      'organizerAdmin.status': 'STATUS',
      'organizerAdmin.actions': 'ÅTGÄRDER',
      'organizerAdmin.active': 'Aktiv',
      'organizerAdmin.inactive': 'Inaktiv',
      'organizerAdmin.edit': 'Redigera',
      'organizerAdmin.delete': 'Ta bort',
      'organizerAdmin.noOrganizersFound': 'Inga Organisatörer Hittades',
      'organizerAdmin.noOrganizersToShow': 'Det finns inga organisatörer att visa.',
      'organizerAdmin.organizationName': 'Organisationsnamn',
      'organizerAdmin.organizationNameRequired': 'Organisationsnamn krävs',
      'organizerAdmin.organizationNamePlaceholder': 'Ange organisationsnamn',
      'organizerAdmin.contactPersonName': 'Kontaktperson',
      'organizerAdmin.contactPersonNameRequired': 'Kontaktperson krävs',
      'organizerAdmin.contactPersonNamePlaceholder': 'Ange kontaktperson',
      'organizerAdmin.contactPersonEmail': 'E-post',
      'organizerAdmin.contactPersonEmailPlaceholder': 'Ange e-postadress',
      'organizerAdmin.contactPersonPhone': 'Telefon',
      'organizerAdmin.contactPersonPhonePlaceholder': 'Ange telefonnummer',
      'organizerAdmin.website': 'Webbplats',
      'organizerAdmin.websitePlaceholder': 'Ange webbplats',
      'organizerAdmin.descriptionField': 'Beskrivning',
      'organizerAdmin.descriptionPlaceholder': 'Ange beskrivning',

      // Dialog Headers
      'dialog.addSite': 'Lägg till Plats',
      'dialog.addClub': 'Lägg till Klubb',
      'dialog.addUser': 'Lägg till Användare',
      'dialog.createOrganizer': 'Skapa Arrangör',
      'dialog.editSite': 'Redigera Plats',
      'dialog.editClub': 'Redigera Klubb',
      'dialog.editUser': 'Redigera Användare',
      'dialog.editOrganizer': 'Redigera Arrangör',

      // Site Dialog Fields
      'siteDialog.place': 'Plats',
      'siteDialog.placeRequired': 'Plats krävs',
      'siteDialog.placePlaceholder': 'Ex: Broparken',
      'siteDialog.address': 'Adress',
      'siteDialog.addressRequired': 'Adress krävs',
      'siteDialog.addressPlaceholder': 'Ex: Brogatan 1',
      'siteDialog.latitude': 'Latitud',
      'siteDialog.latitudeRequired': 'Latitud krävs',
      'siteDialog.latitudePlaceholder': 'Ex: 59.3293',
      'siteDialog.longitude': 'Longitud',
      'siteDialog.longitudeRequired': 'Longitud krävs',
      'siteDialog.longitudePlaceholder': 'Ex: 18.0686',
      'siteDialog.checkInDistance': 'Incheckning avstånd (meter)',
      'siteDialog.checkInDistanceRequired': 'Incheckning avstånd krävs (i meter)',
      'siteDialog.checkInDistancePlaceholder': 'Ex: 900',
      'siteDialog.description': 'Beskrivning',
      'siteDialog.descriptionPlaceholder': 'Beskrivning för platsen...',
      'siteDialog.uploadImage': 'Ladda upp bild',
      'siteDialog.currentImage': 'Nuvarande bild',
      'siteDialog.changeImage': 'Byt bild',

      // Club Dialog Fields
      'clubDialog.clubName': 'Klubbnamn',
      'clubDialog.clubNameRequired': 'Klubbnamn krävs',
      'clubDialog.clubNamePlaceholder': 'Ange klubbnamn',
      'clubDialog.acpCodeOptional': 'ACP-kod (valfritt)',
      'clubDialog.acpCodePlaceholder': 'Lämna tomt om ingen ACP-kod',
      'clubDialog.acpCodeDescription': 'Lämna tomt om klubben inte har en officiell ACP-kod',

      // Organizer Dialog Fields
      'organizerDialog.organizationName': 'Organisationsnamn',
      'organizerDialog.organizationNameRequired': 'Organisationsnamn krävs',
      'organizerDialog.organizationNamePlaceholder': 'Ange organisationsnamn',
      'organizerDialog.contactPersonName': 'Kontaktpersonens namn',
      'organizerDialog.contactPersonNameRequired': 'Kontaktpersonens namn krävs',
      'organizerDialog.contactPersonNamePlaceholder': 'Ange kontaktpersonens namn',
      'organizerDialog.email': 'E-post',
      'organizerDialog.emailRequired': 'E-post krävs',
      'organizerDialog.emailPlaceholder': 'Ange e-postadress',
      'organizerDialog.website': 'Webbsida',
      'organizerDialog.websitePlaceholder': 'Ange webbsida URL',
      'organizerDialog.paymentWebsite': 'Betalningswebbsida',
      'organizerDialog.paymentWebsitePlaceholder': 'Ange betalningswebbsida URL',
      'organizerDialog.status': 'Status',
      'organizerDialog.active': 'Aktiv',
      'organizerDialog.inactive': 'Inaktiv',
      'organizerDialog.logoSvg': 'Logotyp SVG',
      'organizerDialog.logoSvgPlaceholder': 'Ange SVG-kod för logotyp',
      'organizerDialog.description': 'Beskrivning',
      'organizerDialog.descriptionPlaceholder': 'Ange organisationsbeskrivning',
      'organizerDialog.createOrganizer': 'Skapa Arrangör',

      // Event Admin
      'eventAdmin.title': 'Hantera Evenemang',
      'eventAdmin.description': 'Hantera och organisera alla evenemang',

      // Month Names
      'months.january': 'Januari',
      'months.february': 'Februari',
      'months.march': 'Mars',
      'months.april': 'April',
      'months.may': 'Maj',
      'months.june': 'Juni',
      'months.july': 'Juli',
      'months.august': 'Augusti',
      'months.september': 'September',
      'months.october': 'Oktober',
      'months.november': 'November',
      'months.december': 'December',

      // Volunteer
      'volunteer.title': 'Volontär Kontroll',
      'volunteer.description': 'Hantera deltagare vid din kontroll',
      'volunteer.helpTooltip': 'Hjälp och instruktioner',
      'volunteer.howItWorks': 'Hur fungerar det?',
      'volunteer.toSeeParticipants': 'För att se deltagare vid din kontroll:',
      'volunteer.step1': 'Välj event och bana i sidopanelen',
      'volunteer.step2': 'Välj din kontroll från listan',
      'volunteer.step3': 'Se deltagare som ska passera eller har passerat',
      'volunteer.asVolunteer': 'Som volontär kan du:',
      'volunteer.checkInParticipants': 'Checka in deltagare',
      'volunteer.markDNF': 'Markera DNF (Did Not Finish)',
      'volunteer.undoActions': 'Ångra tidigare åtgärder',
      'volunteer.event': 'Event:',
      'volunteer.checkpoint': 'Kontroll:',
      'volunteer.started': 'Startade',
      'volunteer.checkedIn': 'Checkade in',
      'volunteer.checkedOut': 'Checkade ut',
      'volunteer.atCheckpoint': 'Vid kontroll',
      'volunteer.expected': 'Förväntade',
      'volunteer.manageParticipants': 'Hantera deltagare vid din kontroll',
      'volunteer.selectEvent': 'Välj event',
      'volunteer.selectTrack': 'Välj bana',
      'volunteer.selectCheckpoint': 'Välj kontroll',
      'volunteer.searchParticipants': 'Sök deltagare...',
      'volunteer.selectedCheckpoint': 'Vald kontroll',
      'volunteer.startNumber': 'Startnr',
      'volunteer.name': 'Namn',
      'volunteer.lastNameFirstName': 'Efternamn, Förnamn',
      'volunteer.time': 'Tid',
      'volunteer.passed': 'Passerade',
      'volunteer.status': 'Status',
      'volunteer.checkInOut': 'Checka in/ut',
      'volunteer.actions': 'Åtgärder',
      'volunteer.volunteer': 'Volontär',
      'volunteer.undoCheckIn': 'Ångra Check-in',
      'volunteer.checkIn': 'Check-in',
      'volunteer.undoCheckOut': 'Ångra Check-out',
      'volunteer.checkOut': 'Check-out',
      'volunteer.undoDNF': 'Ångra DNF',
      'volunteer.dnf': 'DNF',
      'volunteer.noParticipantsFound': 'Inga deltagare hittades',
      'volunteer.selectCheckpointToSee': 'Välj en kontroll för att se deltagare',
      'volunteer.showingResults': 'Visar {first} till {last} av {totalRecords} deltagare',
      'volunteer.totalParticipants': 'totalt',
      'volunteer.remaining': 'av kvarvarande',
      'volunteer.expectedPercentage': 'av',

      // MSR
      'msr.title': 'MSR',
      'msr.overview': 'MSR - Översikt',
      'msr.participants': 'MSR - Deltagare',
      'msr.other': 'MSR - Övrigt',
      'msr.description': 'MSR (Midnight Sun Race) administration och översikt',
      'msr.participantsDescription': 'Hantera och visa deltagare för MSR-evenemang',
      'msr.otherDescription': 'Översikt över valfria produkter och tjänster för icke-deltagare (t.ex. middagsbiljetter, tröjor)',
      'msr.selectEvent': 'Välj evenemang',
      'msr.loadingEvents': 'Laddar evenemang...',
      'msr.loadingStats': 'Laddar statistik...',
      'msr.loadingParticipants': 'Laddar deltagare...',
      'msr.loadingOptionals': 'Laddar valfria produkter...',
      'msr.noEventsFound': 'Inga evenemang hittades',
      'msr.noStatsFound': 'Ingen statistik hittades',
      'msr.noParticipantsFound': 'Inga deltagare hittades',
      'msr.noOptionalsFound': 'Inga valfria produkter hittades',
      'msr.errorLoadingEvents': 'Kunde inte ladda MSR-evenemang. Försök igen senare.',
      'msr.errorLoadingStats': 'Kunde inte ladda statistik. Försök igen senare.',
      'msr.errorLoadingParticipants': 'Kunde inte ladda deltagare. Försök igen senare.',
      'msr.errorLoadingOptionals': 'Kunde inte ladda valfria produkter. Försök igen senare.',
      'msr.totalRegistrations': 'Totalt antal registreringar',
      'msr.confirmedRegistrations': 'Bekräftade registreringar',
      'msr.totalReservations': 'Totalt antal reservationer',
      'msr.maxRegistrations': 'Max antal registreringar',
      'msr.registrationPercentage': 'Registreringsprocent',
      'msr.optionalProducts': 'Valfria produkter',
      'msr.registrationTrends': 'Registreringsutveckling',
      'msr.last7Days': 'Senaste 7 dagarna',
      'msr.last30Days': 'Senaste 30 dagarna',
      'msr.searchPlaceholder': 'Sök efter evenemang...',
      'msr.searchParticipantsPlaceholder': 'Sök efter namn eller e-post...',
      'msr.searchOptionalsPlaceholder': 'Sök efter produkt, namn eller e-post...',
      'msr.filterByStatus': 'Status:',
      'msr.filterByProduct': 'Produkt:',
      'msr.filterByDate': 'Datum:',
      'msr.allStatuses': 'Alla statusar',
      'msr.allProducts': 'Alla produkter',
      'msr.confirmed': 'Bekräftad',
      'msr.reservation': 'Reservation',
      'msr.startDate': 'Startdatum',
      'msr.endDate': 'Slutdatum',
      'msr.filterType': 'Filtrera efter:',
      'msr.filterByEvent': 'Evenemang',
      'msr.activeFilters': 'Aktiva filter:',
      'msr.productNotFound': 'Produkt inte hittad',
      'msr.exportCsv': 'Exportera CSV',
      'msr.refresh': 'Uppdatera',
      'msr.loadStats': 'Ladda statistik',
      'msr.loadParticipants': 'Ladda deltagare',
      'msr.loadOptionals': 'Ladda valfria produkter',
      'msr.showingResults': 'Visar',
      'msr.of': 'av',
      'msr.products': 'produkter',
      'msr.filtered': 'filtrerat',
      'msr.registrations': 'registreringar',
      'msr.noResultsMatchFilters': 'Inga produkter matchar de valda filtren.',
      'msr.csvExportTooltip': 'Kommer att sparas som:',
      'msr.csvExportNoData': 'Inga registreringar att exportera',
      'msr.spots': 'platser',
      'msr.name': 'Namn',
      'msr.email': 'E-post',
      'msr.quantity': 'Antal',
      'msr.additionalInfo': 'Ytterligare information',
      'msr.registrationDate': 'Registreringsdatum',
      'msr.event': 'Evenemang',
      'msr.status': 'Status',
      'msr.product': 'Produkt',
      'msr.exportedFrom': 'Exporterad från',
      'msr.exportedAt': 'Exporterad',
      'msr.numberOfParticipants': 'Antal deltagare',
      'msr.numberOfRegistrations': 'Antal registreringar',
      'msr.numberOfProducts': 'Antal produkter'
    },
    en: {
      // Common
      'common.loading': 'Loading...',
      'common.error': 'Error',
      'common.success': 'Success',
      'common.cancel': 'Cancel',
      'common.map': 'Map',
      'common.confirm': 'Confirm',
      'common.summary': 'Summary',
      'common.yes': 'Yes',
      'common.no': 'No',
      'common.save': 'Save',
      'common.edit': 'Edit',
      'common.delete': 'Delete',
      'common.add': 'Add',
      'common.close': 'Close',
      'common.back': 'Back',
      'common.next': 'Next',
      'common.previous': 'Previous',
      'common.search': 'Search',
      'common.filter': 'Filter',
      'common.sort': 'Sort',
      'common.refresh': 'Refresh',
      'common.export': 'Export',
      'common.import': 'Import',
      'common.print': 'Print',
      'common.download': 'Download',
      'common.upload': 'Upload',
      'common.update': 'Update',
      'common.create': 'Create',
      'common.view': 'View',
      'common.details': 'Details',
      'common.actions': 'Actions',
      'common.select': 'Select',
      'common.clear': 'Clear',
      'common.reset': 'Reset',
      'common.submit': 'Submit',
      'common.continue': 'Continue',
      'common.finish': 'Finish',
      'common.start': 'Start',
      'common.stop': 'Stop',
      'common.pause': 'Pause',
      'common.resume': 'Resume',
      'common.retry': 'Retry',
      'common.undo': 'Undo',
      'common.redo': 'Redo',
      'common.copy': 'Copy',
      'common.paste': 'Paste',
      'common.cut': 'Cut',
      'common.selectAll': 'Select All',
      'common.none': 'None',
      'common.all': 'All',
      'common.optional': 'Optional',
      'common.required': 'Required',
      'common.and': 'and',
      'common.to': 'to',

      // Navigation
      'nav.dashboard': 'Dashboard',
      'nav.tracks': 'Tracks',
      'nav.trackList': 'Track List',
      'nav.participants': 'Participants',
      'nav.events': 'Events',
      'nav.reports': 'Reports',
      'nav.clubs': 'Clubs',
      'nav.checkpoints': 'Checkpoints',
      'nav.users': 'Users',
      'nav.organizers': 'Organizers',
      'nav.settings': 'Settings',
      'nav.profile': 'Profile',
      'nav.logout': 'Logout',
      'nav.home': 'Home',
      'nav.admin': 'Admin',
      'nav.volunteer': 'Volunteer',
      'nav.competitor': 'Competitor',
      'nav.participantList': 'Participant List',
      'nav.uploadParticipants': 'Upload Participants',
      'nav.createNewEvent': 'Create New Event',
      'nav.gpxImport': 'GPX Import',
      'nav.copyTrack': 'Copy Track',
      'nav.reportToAcp': 'Report to ACP',
      'nav.system': 'System',

      // Login
      'login.title': 'Digital Brevet Card with GPS Validation',
      'login.username': 'Username',
      'login.password': 'Password',
      'login.loginButton': 'Login',
      'login.forgotPassword': 'Forgot Password?',
      'login.rememberMe': 'Remember Me',
      'login.invalidCredentials': 'Invalid username or password',
      'login.loginSuccess': 'Login successful',
      'login.loginError': 'Login failed',
      'login.competitorNotAllowed': 'Competitors should use the competitor app',

      // Admin
      'admin.dashboard': 'Admin Dashboard',
      'admin.overview': 'Overview',
      'admin.statistics': 'Statistics',
      'admin.management': 'Management',
      'admin.settings': 'Admin Settings',
      'admin.users': 'User Management',
      'admin.permissions': 'Permissions',
      'admin.roles': 'Roles',
      'admin.audit': 'Audit Log',

      // Competitor
      'competitor.dashboard': 'Competitor Dashboard',
      'competitor.myTracks': 'My Tracks',
      'competitor.myResults': 'My Results',
      'competitor.profile': 'My Profile',
      'competitor.registration': 'Registration',
      'competitor.trackSelection': 'Track Selection',
      'competitor.payment': 'Payment',
      'competitor.confirmation': 'Confirmation',

      // Volunteer
      'volunteer.dashboard': 'Volunteer Dashboard',
      'volunteer.checkpoints': 'Checkpoints',
      'volunteer.participants': 'Participants',
      'volunteer.tracking': 'Tracking',
      'volunteer.reports': 'Reports',
      'volunteer.schedule': 'Schedule',
      'volunteer.tasks': 'Tasks',

      // Forms
      'form.name': 'Name',
      'form.email': 'Email',
      'form.phone': 'Phone',
      'form.address': 'Address',
      'form.city': 'City',
      'form.postalCode': 'Postal Code',
      'form.country': 'Country',
      'form.date': 'Date',
      'form.time': 'Time',
      'form.dateTime': 'Date & Time',
      'form.startDate': 'Start Date',
      'form.endDate': 'End Date',
      'form.duration': 'Duration',
      'form.distance': 'Distance',
      'form.elevation': 'Elevation',
      'form.difficulty': 'Difficulty',
      'form.description': 'Description',
      'form.notes': 'Notes',
      'form.status': 'Status',
      'form.type': 'Type',
      'form.category': 'Category',
      'form.priority': 'Priority',
      'form.tags': 'Tags',
      'form.file': 'File',
      'form.image': 'Image',
      'form.document': 'Document',
      'form.clubName': 'Club Name',
      'form.enterClubName': 'Enter club name',
      'form.acpCodeOptional': 'ACP Code (Optional)',
      'form.leaveEmptyIfNoAcpCode': 'Leave empty if no ACP code',
      'form.leaveEmptyIfNoAcpCodeDescription': 'Leave this field empty if your club does not have an ACP code',

      // Validation
      'validation.required': 'This field is required',
      'validation.email': 'Please enter a valid email address',
      'validation.phone': 'Please enter a valid phone number',
      'validation.minLength': 'Minimum length is {0} characters',
      'validation.maxLength': 'Maximum length is {0} characters',
      'validation.min': 'Minimum value is {0}',
      'validation.max': 'Maximum value is {0}',
      'validation.pattern': 'Please enter a valid format',
      'validation.confirmPassword': 'Passwords do not match',
      'validation.unique': 'This value already exists',
      'validation.date': 'Please enter a valid date',
      'validation.time': 'Please enter a valid time',
      'validation.futureDate': 'Date must be in the future',
      'validation.pastDate': 'Date must be in the past',
      'validation.clubNameRequired': 'Club name is required',

      // Messages
      'message.success': 'Success',
      'message.error': 'Error',
      'message.warning': 'Warning',
      'message.info': 'Information',
      'message.confirm': 'Are you sure?',
      'message.unsavedChanges': 'You have unsaved changes. Are you sure you want to leave?',
      'message.dataSaved': 'Data saved successfully',
      'message.dataUpdated': 'Data updated successfully',
      'message.dataDeleted': 'Data deleted successfully',
      'message.dataLoaded': 'Data loaded successfully',
      'message.operationFailed': 'Operation failed',
      'message.networkError': 'Network error occurred',
      'message.serverError': 'Server error occurred',
      'message.unauthorized': 'Unauthorized access',
      'message.forbidden': 'Access forbidden',
      'message.notFound': 'Resource not found',
      'message.timeout': 'Request timeout',
      'message.retry': 'Please try again',

      // Status
      'status.active': 'Active',
      'status.inactive': 'Inactive',
      'status.pending': 'Pending',
      'status.approved': 'Approved',
      'status.rejected': 'Rejected',
      'status.completed': 'Completed',
      'status.cancelled': 'Cancelled',
      'status.draft': 'Draft',
      'status.published': 'Published',
      'status.archived': 'Archived',
      'status.deleted': 'Deleted',
      'status.enabled': 'Enabled',
      'status.disabled': 'Disabled',
      'status.available': 'Available',
      'status.unavailable': 'Unavailable',
      'status.online': 'Online',
      'status.offline': 'Offline',
      'status.connected': 'Connected',
      'status.disconnected': 'Disconnected',
      'status.synced': 'Synced',
      'status.syncing': 'Syncing',
      'status.started': 'Started',
      'status.stopped': 'Stopped',
      'status.paused': 'Paused',
      'status.resumed': 'Resumed',

      // Coming Soon
      'comingSoon.gpxImport': 'GPX Import',
      'comingSoon.copyTrack': 'Copy Track',
      'comingSoon.badge': 'Coming Soon',

      // Dashboard/Overview
      'dashboard.welcome': 'Welcome to eBrevet',
      'dashboard.welcomeBack': 'Welcome back! Here is your overview',
      'dashboard.overview': 'Overview',
      'dashboard.overviewAndStats': 'Overview and statistics',
      'dashboard.totalParticipants': 'Total participants',
      'dashboard.activeTracks': 'Active tracks',
      'dashboard.activeEvents': 'Active events',
      'dashboard.completed': 'Completed',
      'dashboard.registered': 'Registered',
      'dashboard.dns': 'DNS',
      'dashboard.latestRegistration': 'Latest registration',
      'dashboard.topPerformingTracks': 'Top performing tracks',
      'dashboard.mostPopularTracks': 'Most popular tracks (Last 30 days)',
      'dashboard.todaysTracks': 'Today\'s tracks',
      'dashboard.tracksRunningToday': 'Tracks running today',
      'dashboard.last7Days': 'Last 7 days',
      'dashboard.today': 'Today',
      'dashboard.thisYear': 'This year',
      'dashboard.total': 'Total',
      'dashboard.tracks': 'tracks',
      'dashboard.events': 'events',
      'dashboard.participants': 'participants',
      'dashboard.addNewTrack': 'Add new track',
      'dashboard.activeEventsRunning': 'Active events running',
      'dashboard.didNotStart': 'Did not start',
      'dashboard.totalRegistered': 'Total registered',
      'dashboard.newRegistration': 'New registration',
      'dashboard.noRegistrationsYet': 'No registrations yet',
      'dashboard.noTracksLast30Days': 'No tracks with participants in the last 30 days',
      'dashboard.noTracksRunningToday': 'No tracks running today',
      'dashboard.heightDifference': 'm height difference',
      'dashboard.developedBy': 'Developed by',
      'dashboard.dailyParticipants': 'Daily participants',
      'dashboard.startedToday': 'Started today',
      'dashboard.brokeToday': 'Broke today',
      'dashboard.didNotStartToday': 'Did not start today',
      'dashboard.weekly': 'Weekly',
      'dashboard.eventGroup': 'Event Group',
      'dashboard.manageEventGroups': 'Manage event groups',
      'dashboard.manageTracks': 'Manage tracks',
      'dashboard.manageParticipants': 'Manage participants',
      'dashboard.manageClubs': 'Manage clubs',
      'dashboard.managePlaces': 'Manage places',
      'dashboard.manageCheckpoints': 'Manage checkpoints',
      'dashboard.manageUsers': 'Manage users',
      'dashboard.manageOrganizers': 'Manage organizers',

      // Participant Management
      'participant.manageParticipants': 'Manage Participants',
      'participant.manageParticipantsDescription': 'Manage participants, registrations and results for events',
      'participant.participantList': 'Participant List',
      'participant.participantListDescription': 'View and manage all participants',
      'participant.upload': 'Upload',
      'participant.uploadDescription': 'Import participants from file',
      'participant.searchParticipants': 'Search participants...',
      'participant.exportStartList': 'Export start list',
      'participant.exportHomologation': 'Export homologation',
      'participant.publishResults': 'Publish results',
      'participant.unpublishResults': 'Unpublish results',
      'participant.selectTrackFirst': 'Select a track first',
      'participant.trackMustBePublished': 'Track must be published first',
      'participant.moveAllParticipants': 'Move All Participants',
      'participant.startNumber': 'Start Number',
      'participant.lastName': 'Last Name',
      'participant.firstName': 'First Name',
      'participant.club': 'Club',
      'participant.actions': 'Actions',
      'participant.move': 'Move',
      'participant.moveTooltip': 'Move participant to another track',
      'participant.edit': 'Edit',
      'participant.delete': 'Delete',
      'participant.address': 'Address',
      'participant.city': 'City',
      'participant.country': 'Country',
      'participant.phone': 'Phone',
      'participant.email': 'Email',
      'participant.finishTime': 'Finish Time',
      'participant.homologationNumber': 'Homologation No.',
      'participant.noParticipantsFound': 'No participants found',
      'participant.showingParticipants': 'Showing {first} to {last} of {totalRecords} participants',

      // Checkpoint Table
      'checkpoint.logo': 'Logo',
      'checkpoint.address': 'Address',
      'checkpoint.city': 'City',
      'checkpoint.distance': 'Distance',
      'checkpoint.checkins': 'Check-ins',
      'checkpoint.checkouts': 'Check-outs',
      'checkpoint.action': 'Action',
      'checkpoint.changeTime': 'Change time',
      'checkpoint.undo': 'Undo',
      'checkpoint.checkin': 'Check in',
      'checkpoint.checkout': 'Check out',
      'checkpoint.undoCheckout': 'Undo checkout',
      'checkpoint.selectTrackToSeeCheckpoints': 'Select a track to see checkpoints',

      // Edit Checkpoint Time Dialog
      'checkpointDialog.changeCheckinTime': 'Change check-in time',
      'checkpointDialog.changeCheckoutTime': 'Change check-out time',
      'checkpointDialog.enterDateAndTime': 'Enter date and time',
      'checkpointDialog.cancel': 'Cancel',
      'checkpointDialog.save': 'Save',

      // Upload Participants
      'upload.uploadParticipants': 'Upload Participants',
      'upload.uploadParticipantsDescription': 'Here you can upload participants with a CSV file',
      'upload.csvFormatForParticipants': 'CSV format for participants',
      'upload.sixteenColumnsSeparatedBySemicolon': '16 columns separated by semicolon (;)',
      'upload.startNumber': 'Start Number',
      'upload.firstName': 'First Name',
      'upload.lastName': 'Last Name',
      'upload.gender': 'Gender (Male/Female)',
      'upload.clubName': 'Club Name',
      'upload.address': 'Address',
      'upload.postalCode': 'Postal Code',
      'upload.city': 'City',
      'upload.country': 'Country',
      'upload.email': 'Email',
      'upload.phone': 'Phone',
      'upload.registrationDate': 'Registration Date (YYYY-MM-DD HH:MM:SS)',
      'upload.birthYear': 'Birth Year (YYYY)',
      'upload.referenceNumber': 'Reference Number',
      'upload.physicalBrevetCard': 'Physical brevet card (Yes/No or 1/0)',
      'upload.additionalInformation': 'Additional Information',
      'upload.example': 'Example:',
      'upload.trackActive': 'Track Active',
      'upload.trackInactive': 'Track Inactive',
      'upload.uploadNotAllowedForInactiveTracks': 'Upload of participants is not allowed for inactive tracks.',
      'upload.chooseCsv': 'Choose CSV',
      'upload.uploadCsv': 'Upload CSV',
      'upload.selectedFiles': 'Selected Files',
      'upload.uploadedFile': 'Uploaded File',
      'upload.uploadResult': 'Upload Result',
      'upload.totalRows': 'Total Rows',
      'upload.successful': 'Successful',
      'upload.skipped': 'Skipped',
      'upload.failed': 'Failed',
      'upload.detailedErrors': 'Detailed Errors:',
      'upload.row': 'Row',
      'upload.data': 'Data',
      'upload.createdParticipants': 'Created Participants:',
      'upload.participant': 'Participant',
      'upload.uploadComplete': 'Upload Complete',
      'upload.uploadIssues': 'Upload Issues',
      'upload.uploadFailed': 'Upload Failed',

      // Track Management
      'track.manageTracks': 'Manage Tracks',
      'track.manageTracksDescription': 'Manage tracks, checkpoints and events',
      'track.trackList': 'Track List',
      'track.trackListDescription': 'Manage and view all tracks',
      'track.trackBuilder': 'Track Builder',
      'track.trackBuilderDescription': 'Create and edit tracks',
      'track.openTrackBuilder': 'Open track builder',
      'track.searchTracks': 'Search tracks...',
      'track.event': 'Event',
      'track.startDate': 'Start Date',
      'track.start': 'Start',
      'track.endDate': 'End Date',
      'track.end': 'End',
      'track.status': 'Status',
      'track.trackingLink': 'Link to tracking page',
      'track.tracking': 'Tracking',
      'track.action': 'Action',
      'track.completed': 'Completed',
      'track.ongoing': 'Ongoing',
      'track.cancelled': 'Cancelled',
      'track.followParticipantsOnEvent': 'Follow participants on event',
      'track.remove': 'Remove',
      'track.noTracksFound': 'No tracks found',
      'track.noTracksToShow': 'There are no tracks to show.',
      'track.track': 'Track',
      'track.trackLink': 'Link to track',
      'track.date': 'Date',
      'track.distance': 'Distance',
      'track.cyclistView': 'Cyclist view',
      'track.trackingPage': 'Tracking page',
      'track.followParticipantsOnTrack': 'Follow participants on track',
      'track.noTracksOnEvent': 'There are no tracks on this event.',
      'track.controls': 'Controls',

      // Checkpoint Table (General)
      'checkpointTable.logo': 'Logo',
      'checkpointTable.address': 'Address',
      'checkpointTable.city': 'City',
      'checkpointTable.distance': 'Distance',
      'checkpointTable.opens': 'Opens',
      'checkpointTable.closes': 'Closes',
      'checkpointTable.noCheckpointsFound': 'No checkpoints found',
      'checkpointTable.noCheckpointsOnTrack': 'There are no checkpoints on this track.',

      // Event Creation
      'event.createEvent': 'Create New Event',
      'event.addEvent': 'Add Event',
      'event.title': 'Title',
      'event.titleRequired': 'Title is required',
      'event.titlePlaceholder': 'Ex: Moonlight Brevet',
      'event.startDate': 'Start Date',
      'event.endDate': 'End Date',
      'event.status': 'Status',
      'event.description': 'Description',
      'event.descriptionPlaceholder': 'Enter event description...',
      'event.statusActive': 'Active',
      'event.statusCancelled': 'Cancelled',
      'event.statusCompleted': 'Completed',
      'event.cancel': 'Cancel',
      'event.save': 'Save',
      'event.newEvent': 'New Event',
      'event.searchEvents': 'Search Event Groups...',
      'event.refresh': 'Refresh',
      'event.name': 'NAME',
      'event.actions': 'ACTIONS',
      'event.edit': 'Edit',
      'event.delete': 'Delete',
      'event.noEventsFound': 'No Events Found',
      'event.noEventsToShow': 'There are no events to show.',
      'event.completed': 'Completed',
      'event.ongoing': 'Ongoing',
      'event.cancelled': 'Cancelled',

      // Track Builder - Event Creation
      'trackBuilder.createNewEvent': 'Create New Event',
      'trackBuilder.createNewEventDescription': 'Create a completely new event in the race service with calendar entry',
      'trackBuilder.buildFromGpx': 'Build from GPX file',
      'trackBuilder.buildFromGpxDescription': 'Import a GPX file to automatically create track and checkpoints',
      'trackBuilder.copyExistingTrack': 'Copy existing track',
      'trackBuilder.copyExistingTrackDescription': 'Create a new track based on an existing track',
      'trackBuilder.upcomingFeature': 'Upcoming feature',
      'trackBuilder.fillInformation': 'Create New Event',
      'trackBuilder.fillInformationDescription': 'Fill in the information below to create your event and track',

      // Track Builder - Track Info Form
      'trackInfo.eventGroup': 'Event Group',
      'trackInfo.selectEventGroup': 'Select event group',
      'trackInfo.organizer': 'Organizer',
      'trackInfo.selectOrganizer': 'Select organizer',
      'trackInfo.basicInformation': 'Basic Information',
      'trackInfo.eventName': 'Event Name',
      'trackInfo.eventNameTooltip': 'Track name e.g. MSR 1200',
      'trackInfo.eventNamePlaceholder': 'e.g. BRM 300 Bönhamn',
      'trackInfo.distance': 'Distance (km)',
      'trackInfo.distancePlaceholder': 'Select or enter custom distance',
      'trackInfo.distanceTooltip': 'Select a standard distance or enter custom',
      'trackInfo.eventType': 'Event Type',
      'trackInfo.eventTypePlaceholder': 'Select event type',
      'trackInfo.eventTypeTooltip': 'Select event type',
      'trackInfo.elevation': 'Elevation (m)',
      'trackInfo.elevationPlaceholder': '1700',
      'trackInfo.elevationTooltip': 'Total elevation gain in meters',
      'trackInfo.startDate': 'Start Date',
      'trackInfo.startDatePlaceholder': 'YYYY-MM-DD',
      'trackInfo.startDateTooltip': 'If left empty, start date will be automatically set to next day',
      'trackInfo.startTime': 'Start Time',
      'trackInfo.startTimePlaceholder': '07:00',
      'trackInfo.startTimeTooltip': '24-hour format, e.g. 07:00',
      'trackInfo.descriptionAndLinks': 'Description and Links',
      'trackInfo.description': 'Description',
      'trackInfo.descriptionPlaceholder': 'Describe the event...',
      'trackInfo.startLocation': 'Start Location',
      'trackInfo.startLocationPlaceholder': 'e.g. ICA Kvantum, Norrköping',
      'trackInfo.payment': 'Payment',
      'trackInfo.paymentPlaceholder': 'e.g. Swish 123 456 78 90',
      'trackInfo.trackLink': 'Track Link',
      'trackInfo.trackLinkTooltip': 'Link to track on Strava',
      'trackInfo.trackLinkPlaceholder': 'https://www.ridewithgps.com/routes/...',
      'trackInfo.settings': 'Settings',
      'trackInfo.maxParticipants': 'Max Participants',
      'trackInfo.maxParticipantsTooltip': 'Maximum number of participants',
      'trackInfo.registrationOpens': 'Registration Opens',
      'trackInfo.registrationOpensPlaceholder': 'YYYY-MM-DD HH:MM',
      'trackInfo.registrationOpensTooltip': 'Date and time when registration opens',
      'trackInfo.registrationCloses': 'Registration Closes',
      'trackInfo.registrationClosesPlaceholder': 'YYYY-MM-DD HH:MM',
      'trackInfo.registrationClosesTooltip': 'Date and time when registration closes',
      'trackInfo.stripePayment': 'Stripe Card Payment',
      'trackInfo.stripePaymentDesc': 'Enable secure card payment',
      'trackInfo.emailConfirmation': 'Email Confirmation',
      'trackInfo.emailConfirmationDesc': 'Send confirmation upon registration',

      // Track Builder - Controls Form
      'controls.title': 'Controls',
      'controls.generateByDistance': 'Generate by distance',
      'controls.addControl': 'Add Control',
      'controls.controlNumber': 'Control #',
      'controls.removeControl': 'Remove Control',
      'controls.controlSite': 'Control Site',
      'controls.distance': 'Distance (km)',
      'controls.distanceTooltip': 'Distance to control point in kilometers',
      'controls.noControlsYet': 'No controls yet',
      'controls.addFirstControl': 'Add your first control to get started',
      'controls.addFirstControlButton': 'Add First Control',

      // Track Builder - Summary
      'summary.preview': 'Preview - This is how the event will appear',
      'summary.previewDescription': 'Preview - This is how the event will appear',
      'summary.distance': 'Distance:',
      'summary.elevation': 'Elevation:',
      'summary.startDate': 'Start Date:',
      'summary.startTime': 'Start Time:',
      'summary.lastRegistration': 'Last Registration:',
      'summary.startLocation': 'Start Location:',
      'summary.organizer': 'Organizer:',
      'summary.paymentVia': 'Payment via:',
      'summary.other': 'Other:',
      'summary.trackLink': 'Track Link',
      'summary.startList': 'Start List',
      'summary.eventGroup': 'Event Group',
      'summary.selectEventToShow': 'Select an event to show information here',
      'summary.track': 'Track',
      'summary.date': 'Date',
      'summary.minTime': 'Min time:',
      'summary.maxTime': 'Max time:',
      'summary.viewOnStrava': 'View on Strava',
      'summary.fillTrackInfoAndAddControls': 'Fill in track information and add controls to calculate the track',
      'summary.controls': 'Controls',
      'summary.control': 'control',
      'summary.controlsPlural': 'controls',
      'summary.noControlsAddedYet': 'No controls added yet',
      'summary.savingTrack': 'Saving track...',
      'summary.complete': 'Complete',

      // System Administration - User Admin
      'userAdmin.title': 'User Management',
      'userAdmin.description': 'Manage and organize all users',
      'userAdmin.searchUsers': 'Search users...',
      'userAdmin.newUser': 'New User',
      'userAdmin.refresh': 'Refresh',
      'userAdmin.name': 'NAME',
      'userAdmin.email': 'EMAIL',
      'userAdmin.permissions': 'PERMISSIONS',
      'userAdmin.status': 'STATUS',
      'userAdmin.actions': 'ACTIONS',
      'userAdmin.active': 'Active',
      'userAdmin.inactive': 'Inactive',
      'userAdmin.edit': 'Edit',
      'userAdmin.delete': 'Delete',
      'userAdmin.noUsersFound': 'No Users Found',
      'userAdmin.noUsersToShow': 'There are no users to show.',
      'userAdmin.userInformation': 'User Information',
      'userAdmin.firstName': 'First Name',
      'userAdmin.firstNameRequired': 'First name is required',
      'userAdmin.firstNamePlaceholder': 'Enter first name',
      'userAdmin.lastName': 'Last Name',
      'userAdmin.lastNameRequired': 'Last name is required',
      'userAdmin.lastNamePlaceholder': 'Enter last name',
      'userAdmin.username': 'Username',
      'userAdmin.usernameRequired': 'Username is required',
      'userAdmin.usernamePlaceholder': 'Enter username',
      'userAdmin.password': 'Password',
      'userAdmin.passwordPlaceholder': 'Enter or generate password',
      'userAdmin.generatePassword': 'Generate password',
      'userAdmin.contactInformation': 'Contact Information',
      'userAdmin.phone': 'Phone',
      'userAdmin.phonePlaceholder': 'Enter phone number',
      'userAdmin.emailField': 'Email',
      'userAdmin.emailPlaceholder': 'Enter email address',
      'userAdmin.organizer': 'Organizer',
      'userAdmin.selectOrganizer': 'Select organizer',
      'userAdmin.selectOrganizerOptional': 'Select organizer (optional)',
      'userAdmin.organizerDescription': 'Select an organizer to associate with the user (optional)',
      'userAdmin.superuser': 'Superuser',
      'userAdmin.superuserDescription': 'Superuser with all permissions',
      'userAdmin.admin': 'Administrator',
      'userAdmin.adminDescription': 'Administrator with read and write permissions',
      'userAdmin.user': 'User',
      'userAdmin.userDescription': 'Read permissions and limited write permissions',
      'userAdmin.volunteer': 'Volunteer',
      'userAdmin.volunteerDescription': 'Permission to check in and view passages at controls',
      'userAdmin.developer': 'Developer',
      'userAdmin.developerDescription': 'Special permissions for developers',

      // System Administration - Club Admin
      'clubAdmin.title': 'Manage Clubs',
      'clubAdmin.description': 'Manage and organize all clubs',
      'clubAdmin.searchClubs': 'Search clubs...',
      'clubAdmin.newClub': 'New Club',
      'clubAdmin.refresh': 'Refresh',
      'clubAdmin.name': 'NAME',
      'clubAdmin.acpCode': 'ACP CODE',
      'clubAdmin.actions': 'ACTIONS',
      'clubAdmin.officialAcpClub': 'Official ACP Club',
      'clubAdmin.noClubsFound': 'No Clubs Found',
      'clubAdmin.noClubsToShow': 'There are no clubs to show.',
      'clubAdmin.clubName': 'Club Name',
      'clubAdmin.clubNameRequired': 'Club name is required',
      'clubAdmin.clubNamePlaceholder': 'Enter club name',
      'clubAdmin.acpCodeField': 'ACP Code',
      'clubAdmin.acpCodePlaceholder': 'Enter ACP code',
      'clubAdmin.acpCodeDescription': 'Official ACP code for the club',

      // System Administration - Site Admin
      'siteAdmin.title': 'Manage Checkpoints',
      'siteAdmin.description': 'Manage and organize all checkpoints',
      'siteAdmin.searchSites': 'Search sites...',
      'siteAdmin.newSite': 'New Site',
      'siteAdmin.refresh': 'Refresh',
      'siteAdmin.name': 'NAME',
      'siteAdmin.location': 'LOCATION',
      'siteAdmin.address': 'ADDRESS',
      'siteAdmin.logo': 'LOGO',
      'siteAdmin.status': 'STATUS',
      'siteAdmin.actions': 'ACTIONS',
      'siteAdmin.active': 'Active',
      'siteAdmin.inactive': 'Inactive',
      'siteAdmin.edit': 'Edit',
      'siteAdmin.delete': 'Delete',
      'siteAdmin.noSitesFound': 'No Sites Found',
      'siteAdmin.noSitesToShow': 'There are no sites to show.',
      'siteAdmin.clickToViewImage': 'Click to view image',
      'siteAdmin.siteName': 'Site Name',
      'siteAdmin.siteNameRequired': 'Site name is required',
      'siteAdmin.siteNamePlaceholder': 'Enter site name',
      'siteAdmin.siteLocation': 'Location',
      'siteAdmin.siteLocationPlaceholder': 'Enter location',
      'siteAdmin.siteAddress': 'Address',
      'siteAdmin.siteAddressPlaceholder': 'Enter address',
      'siteAdmin.siteDescription': 'Description',
      'siteAdmin.siteDescriptionPlaceholder': 'Enter description',
      'siteAdmin.siteImage': 'Image',
      'siteAdmin.siteImageDescription': 'Upload image for the site',

      // System Administration - Organizer Admin
      'organizerAdmin.title': 'Manage Organizers',
      'organizerAdmin.description': 'Manage and organize all organizers',
      'organizerAdmin.searchOrganizers': 'Search organizers...',
      'organizerAdmin.newOrganizer': 'New Organizer',
      'organizerAdmin.refresh': 'Refresh',
      'organizerAdmin.organization': 'ORGANIZATION',
      'organizerAdmin.contactPerson': 'CONTACT PERSON',
      'organizerAdmin.status': 'STATUS',
      'organizerAdmin.actions': 'ACTIONS',
      'organizerAdmin.active': 'Active',
      'organizerAdmin.inactive': 'Inactive',
      'organizerAdmin.edit': 'Edit',
      'organizerAdmin.delete': 'Delete',
      'organizerAdmin.noOrganizersFound': 'No Organizers Found',
      'organizerAdmin.noOrganizersToShow': 'There are no organizers to show.',
      'organizerAdmin.organizationName': 'Organization Name',
      'organizerAdmin.organizationNameRequired': 'Organization name is required',
      'organizerAdmin.organizationNamePlaceholder': 'Enter organization name',
      'organizerAdmin.contactPersonName': 'Contact Person',
      'organizerAdmin.contactPersonNameRequired': 'Contact person is required',
      'organizerAdmin.contactPersonNamePlaceholder': 'Enter contact person',
      'organizerAdmin.contactPersonEmail': 'Email',
      'organizerAdmin.contactPersonEmailPlaceholder': 'Enter email address',
      'organizerAdmin.contactPersonPhone': 'Phone',
      'organizerAdmin.contactPersonPhonePlaceholder': 'Enter phone number',
      'organizerAdmin.website': 'Website',
      'organizerAdmin.websitePlaceholder': 'Enter website',
      'organizerAdmin.descriptionField': 'Description',
      'organizerAdmin.descriptionPlaceholder': 'Enter description',

      // Dialog Headers
      'dialog.addSite': 'Add Site',
      'dialog.addClub': 'Add Club',
      'dialog.addUser': 'Add User',
      'dialog.createOrganizer': 'Create Organizer',
      'dialog.editSite': 'Edit Site',
      'dialog.editClub': 'Edit Club',
      'dialog.editUser': 'Edit User',
      'dialog.editOrganizer': 'Edit Organizer',

      // Site Dialog Fields
      'siteDialog.place': 'Location',
      'siteDialog.placeRequired': 'Location is required',
      'siteDialog.placePlaceholder': 'Ex: Broparken',
      'siteDialog.address': 'Address',
      'siteDialog.addressRequired': 'Address is required',
      'siteDialog.addressPlaceholder': 'Ex: Brogatan 1',
      'siteDialog.latitude': 'Latitude',
      'siteDialog.latitudeRequired': 'Latitude is required',
      'siteDialog.latitudePlaceholder': 'Ex: 59.3293',
      'siteDialog.longitude': 'Longitude',
      'siteDialog.longitudeRequired': 'Longitude is required',
      'siteDialog.longitudePlaceholder': 'Ex: 18.0686',
      'siteDialog.checkInDistance': 'Check-in distance (meters)',
      'siteDialog.checkInDistanceRequired': 'Check-in distance is required (in meters)',
      'siteDialog.checkInDistancePlaceholder': 'Ex: 900',
      'siteDialog.description': 'Description',
      'siteDialog.descriptionPlaceholder': 'Description for the site...',
      'siteDialog.uploadImage': 'Upload image',
      'siteDialog.currentImage': 'Current image',
      'siteDialog.changeImage': 'Change image',

      // Club Dialog Fields
      'clubDialog.clubName': 'Club Name',
      'clubDialog.clubNameRequired': 'Club name is required',
      'clubDialog.clubNamePlaceholder': 'Enter club name',
      'clubDialog.acpCodeOptional': 'ACP Code (optional)',
      'clubDialog.acpCodePlaceholder': 'Leave empty if no ACP code',
      'clubDialog.acpCodeDescription': 'Leave empty if the club does not have an official ACP code',

      // Organizer Dialog Fields
      'organizerDialog.organizationName': 'Organization Name',
      'organizerDialog.organizationNameRequired': 'Organization name is required',
      'organizerDialog.organizationNamePlaceholder': 'Enter organization name',
      'organizerDialog.contactPersonName': 'Contact Person Name',
      'organizerDialog.contactPersonNameRequired': 'Contact person name is required',
      'organizerDialog.contactPersonNamePlaceholder': 'Enter contact person name',
      'organizerDialog.email': 'Email',
      'organizerDialog.emailRequired': 'Email is required',
      'organizerDialog.emailPlaceholder': 'Enter email address',
      'organizerDialog.website': 'Website',
      'organizerDialog.websitePlaceholder': 'Enter website URL',
      'organizerDialog.paymentWebsite': 'Payment Website',
      'organizerDialog.paymentWebsitePlaceholder': 'Enter payment website URL',
      'organizerDialog.status': 'Status',
      'organizerDialog.active': 'Active',
      'organizerDialog.inactive': 'Inactive',
      'organizerDialog.logoSvg': 'Logo SVG',
      'organizerDialog.logoSvgPlaceholder': 'Enter SVG code for logo',
      'organizerDialog.description': 'Description',
      'organizerDialog.descriptionPlaceholder': 'Enter organization description',
      'organizerDialog.createOrganizer': 'Create Organizer',

      // Event Admin
      'eventAdmin.title': 'Manage Events',
      'eventAdmin.description': 'Manage and organize all events',

      // Month Names
      'months.january': 'January',
      'months.february': 'February',
      'months.march': 'March',
      'months.april': 'April',
      'months.may': 'May',
      'months.june': 'June',
      'months.july': 'July',
      'months.august': 'August',
      'months.september': 'September',
      'months.october': 'October',
      'months.november': 'November',
      'months.december': 'December',

      // Volunteer
      'volunteer.title': 'Volunteer Control',
      'volunteer.description': 'Manage participants at your checkpoint',
      'volunteer.helpTooltip': 'Help and instructions',
      'volunteer.howItWorks': 'How does it work?',
      'volunteer.toSeeParticipants': 'To see participants at your checkpoint:',
      'volunteer.step1': 'Select event and track in the sidebar',
      'volunteer.step2': 'Select your checkpoint from the list',
      'volunteer.step3': 'See participants who should pass or have passed',
      'volunteer.asVolunteer': 'As a volunteer you can:',
      'volunteer.checkInParticipants': 'Check in participants',
      'volunteer.markDNF': 'Mark DNF (Did Not Finish)',
      'volunteer.undoActions': 'Undo previous actions',
      'volunteer.event': 'Event:',
      'volunteer.checkpoint': 'Checkpoint:',
      'volunteer.started': 'Started',
      'volunteer.checkedIn': 'Checked In',
      'volunteer.checkedOut': 'Checked Out',
      'volunteer.atCheckpoint': 'At Checkpoint',
      'volunteer.expected': 'Expected',
      'volunteer.manageParticipants': 'Manage participants at your checkpoint',
      'volunteer.selectEvent': 'Select event',
      'volunteer.selectTrack': 'Select track',
      'volunteer.selectCheckpoint': 'Select checkpoint',
      'volunteer.searchParticipants': 'Search participants...',
      'volunteer.selectedCheckpoint': 'Selected checkpoint',
      'volunteer.startNumber': 'Start #',
      'volunteer.name': 'Name',
      'volunteer.lastNameFirstName': 'Last Name, First Name',
      'volunteer.time': 'Time',
      'volunteer.passed': 'Passed',
      'volunteer.status': 'Status',
      'volunteer.checkInOut': 'Check In/Out',
      'volunteer.actions': 'Actions',
      'volunteer.volunteer': 'Volunteer',
      'volunteer.undoCheckIn': 'Undo Check-in',
      'volunteer.checkIn': 'Check-in',
      'volunteer.undoCheckOut': 'Undo Check-out',
      'volunteer.checkOut': 'Check-out',
      'volunteer.undoDNF': 'Undo DNF',
      'volunteer.dnf': 'DNF',
      'volunteer.noParticipantsFound': 'No participants found',
      'volunteer.selectCheckpointToSee': 'Select a checkpoint to see participants',
      'volunteer.showingResults': 'Showing {first} to {last} of {totalRecords} participants',
      'volunteer.totalParticipants': 'total',
      'volunteer.remaining': 'of remaining',
      'volunteer.expectedPercentage': 'of',

      // MSR
      'msr.title': 'MSR',
      'msr.overview': 'MSR - Overview',
      'msr.participants': 'MSR - Participants',
      'msr.other': 'MSR - Other',
      'msr.description': 'MSR (Midnight Sun Race) administration and overview',
      'msr.participantsDescription': 'Manage and view participants for MSR events',
      'msr.otherDescription': 'Overview of optional products and services for non-participants (e.g. dinner tickets, jerseys)',
      'msr.selectEvent': 'Select Event',
      'msr.loadingEvents': 'Loading events...',
      'msr.loadingStats': 'Loading statistics...',
      'msr.loadingParticipants': 'Loading participants...',
      'msr.loadingOptionals': 'Loading optional products...',
      'msr.noEventsFound': 'No events found',
      'msr.noStatsFound': 'No statistics found',
      'msr.noParticipantsFound': 'No participants found',
      'msr.noOptionalsFound': 'No optional products found',
      'msr.errorLoadingEvents': 'Could not load MSR events. Please try again later.',
      'msr.errorLoadingStats': 'Could not load statistics. Please try again later.',
      'msr.errorLoadingParticipants': 'Could not load participants. Please try again later.',
      'msr.errorLoadingOptionals': 'Could not load optional products. Please try again later.',
      'msr.totalRegistrations': 'Total Registrations',
      'msr.confirmedRegistrations': 'Confirmed Registrations',
      'msr.totalReservations': 'Total Reservations',
      'msr.maxRegistrations': 'Max Registrations',
      'msr.registrationPercentage': 'Registration Percentage',
      'msr.optionalProducts': 'Optional Products',
      'msr.registrationTrends': 'Registration Trends',
      'msr.last7Days': 'Last 7 Days',
      'msr.last30Days': 'Last 30 Days',
      'msr.searchPlaceholder': 'Search for events...',
      'msr.searchParticipantsPlaceholder': 'Search by name or email...',
      'msr.searchOptionalsPlaceholder': 'Search for product, name or email...',
      'msr.filterByStatus': 'Status:',
      'msr.filterByProduct': 'Product:',
      'msr.filterByDate': 'Date:',
      'msr.allStatuses': 'All Statuses',
      'msr.allProducts': 'All Products',
      'msr.confirmed': 'Confirmed',
      'msr.reservation': 'Reservation',
      'msr.startDate': 'Start Date',
      'msr.endDate': 'End Date',
      'msr.filterType': 'Filter by:',
      'msr.filterByEvent': 'Event',
      'msr.activeFilters': 'Active Filters:',
      'msr.productNotFound': 'Product not found',
      'msr.exportCsv': 'Export CSV',
      'msr.refresh': 'Refresh',
      'msr.loadStats': 'Load Statistics',
      'msr.loadParticipants': 'Load Participants',
      'msr.loadOptionals': 'Load Optional Products',
      'msr.showingResults': 'Showing',
      'msr.of': 'of',
      'msr.products': 'products',
      'msr.filtered': 'filtered',
      'msr.registrations': 'registrations',
      'msr.noResultsMatchFilters': 'No products match the selected filters.',
      'msr.csvExportTooltip': 'Will be saved as:',
      'msr.csvExportNoData': 'No registrations to export',
      'msr.spots': 'spots',
      'msr.name': 'Name',
      'msr.email': 'Email',
      'msr.quantity': 'Quantity',
      'msr.additionalInfo': 'Additional Info',
      'msr.registrationDate': 'Registration Date',
      'msr.event': 'Event',
      'msr.status': 'Status',
      'msr.product': 'Product',
      'msr.exportedFrom': 'Exported from',
      'msr.exportedAt': 'Exported at',
      'msr.numberOfParticipants': 'Number of participants',
      'msr.numberOfRegistrations': 'Number of registrations',
      'msr.numberOfProducts': 'Number of products'
    }
  };

  translate(key: keyof TranslationKeys): string {
    const currentLang = this.languageService.getCurrentLanguage();
    const translation = this.translations[currentLang]?.[key];
    return translation ? translation : String(key);
  }

  // Method for login screen that uses browser language if no user preference
  translateForDisplay(key: keyof TranslationKeys): string {
    const displayLang = this.languageService.getDisplayLanguage();
    const translation = this.translations[displayLang]?.[key];
    return translation ? translation : String(key);
  }
}
