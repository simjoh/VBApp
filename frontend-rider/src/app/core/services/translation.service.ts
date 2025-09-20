import { Injectable, inject } from '@angular/core';
import { LanguageService, SupportedLanguage } from './language.service';

export interface TranslationKeys {
  // Common
  'common.loading': string;
  'common.error': string;
  'common.success': string;
  'common.cancel': string;
  'common.confirm': string;
  'common.yes': string;
  'common.no': string;

  // Login
  'login.title': string;
  'login.subtitle': string;
  'login.username': string;
  'login.password': string;
  'login.loginButton': string;
  'login.error.invalidCredentials': string;

  // Competitor
  'competitor.title': string;
  'competitor.startNumber': string;
  'competitor.riderName': string;
  'competitor.trackInfo': string;
  'competitor.checkpoints': string;
  'competitor.abandonBrevet': string;
  'competitor.undoAbandon': string;
  'competitor.abandonConfirm': string;
  'competitor.undoAbandonConfirm': string;

  // Checkpoints
  'checkpoint.distance': string;
  'checkpoint.toNext': string;
  'checkpoint.opens': string;
  'checkpoint.closes': string;
  'checkpoint.service': string;
  'checkpoint.time': string;
  'checkpoint.checkIn': string;
  'checkpoint.checkOut': string;
  'checkpoint.checkedIn': string;
  'checkpoint.checkedOut': string;
  'checkpoint.undoCheckOut': string;
  'checkpoint.undoCheckIn': string;
  'checkpoint.finished': string;

  // Geolocation
  'geolocation.title': string;
  'geolocation.message': string;
  'geolocation.allowButton': string;

  // Messages
  'message.brevetAbandoned': string;
  'message.abandonUndone': string;
  'message.abandonFailed': string;
  'message.undoFailed': string;
  'message.checkpointsComingSoon': string;
  'message.locationAccessGranted': string;
  'message.locationAccessDenied': string;

  // Connection
  'connection.offline': string;
  'connection.slowConnection': string;
  'connection.loading': string;
  'connection.initializing': string;
  'connection.almostReady': string;
}

@Injectable({
  providedIn: 'root'
})
export class TranslationService {
  private languageService = inject(LanguageService);

  private translations: Record<SupportedLanguage, TranslationKeys> = {
    en: {
      'common.loading': 'Loading...',
      'common.error': 'Error',
      'common.success': 'Success',
      'common.cancel': 'Cancel',
      'common.confirm': 'Confirm',
      'common.yes': 'Yes',
      'common.no': 'No',
      'login.title': 'Competitor Portal',
      'login.subtitle': 'Riders App',
      'login.username': 'Username',
      'login.password': 'Password',
      'login.loginButton': 'Login',
      'login.error.invalidCredentials': 'Invalid username or password',
      'competitor.title': 'Competitor Dashboard',
      'competitor.startNumber': 'Start Number',
      'competitor.riderName': 'Rider Name',
      'competitor.trackInfo': 'Track Information',
      'competitor.checkpoints': 'Checkpoints',
      'competitor.abandonBrevet': 'Abandon Brevet',
      'competitor.undoAbandon': 'Undo Abandon',
      'competitor.abandonConfirm': 'Are you sure you want to abandon the brevet? This action cannot be undone easily.',
      'competitor.undoAbandonConfirm': 'Are you sure you want to undo the brevet abandonment?',
      'checkpoint.distance': 'Distance',
      'checkpoint.toNext': 'To next',
      'checkpoint.opens': 'Opens',
      'checkpoint.closes': 'Closes',
      'checkpoint.service': 'Service',
      'checkpoint.time': 'Time',
      'checkpoint.checkIn': 'Check In',
      'checkpoint.checkOut': 'Check Out',
      'checkpoint.checkedIn': 'Checked In',
      'checkpoint.checkedOut': 'Checked Out',
      'checkpoint.undoCheckOut': 'Undo Check Out',
      'checkpoint.undoCheckIn': 'Undo Check In',
      'checkpoint.finished': 'Finished',
      'geolocation.title': 'Location Access Required',
      'geolocation.message': 'This app requires location access to track your progress.',
      'geolocation.allowButton': 'Allow Location Access',
      'message.brevetAbandoned': 'You have successfully abandoned the brevet',
      'message.abandonUndone': 'Brevet abandonment has been successfully undone',
      'message.abandonFailed': 'Failed to abandon brevet. Please try again.',
      'message.undoFailed': 'Failed to undo brevet abandonment. Please try again.',
      'message.checkpointsComingSoon': 'Checkpoints for this track will be available soon.',
      'message.locationAccessGranted': 'Location access granted',
      'message.locationAccessDenied': 'Location access denied',
      'connection.offline': 'You are currently offline',
      'connection.slowConnection': 'Slow connection detected - Loading may take longer',
      'connection.loading': 'Loading...',
      'connection.initializing': 'Initializing...',
      'connection.almostReady': 'Almost ready...'
    },
    sv: {
      'common.loading': 'Laddar...',
      'common.error': 'Fel',
      'common.success': 'Framgång',
      'common.cancel': 'Avbryt',
      'common.confirm': 'Bekräfta',
      'common.yes': 'Ja',
      'common.no': 'Nej',
      'login.title': 'Tävlande Portal',
      'login.subtitle': 'Cyklist App',
      'login.username': 'Användarnamn',
      'login.password': 'Lösenord',
      'login.loginButton': 'Logga in',
      'login.error.invalidCredentials': 'Ogiltigt användarnamn eller lösenord',
      'competitor.title': 'Tävlande Dashboard',
      'competitor.startNumber': 'Startnummer',
      'competitor.riderName': 'Cyklist Namn',
      'competitor.trackInfo': 'Bana Information',
      'competitor.checkpoints': 'Kontrollpunkter',
      'competitor.abandonBrevet': 'Överge Brevet',
      'competitor.undoAbandon': 'Ångra Övergivande',
      'competitor.abandonConfirm': 'Är du säker på att du vill överge brevet? Denna åtgärd kan inte ångras enkelt.',
      'competitor.undoAbandonConfirm': 'Är du säker på att du vill ångra övergivandet av brevet?',
      'checkpoint.distance': 'Avstånd',
      'checkpoint.toNext': 'Till nästa',
      'checkpoint.opens': 'Öppnar',
      'checkpoint.closes': 'Stänger',
      'checkpoint.service': 'Service',
      'checkpoint.time': 'Tid',
      'checkpoint.checkIn': 'Checka In',
      'checkpoint.checkOut': 'Checka Ut',
      'checkpoint.checkedIn': 'Incheckad',
      'checkpoint.checkedOut': 'Utcheckad',
      'checkpoint.undoCheckOut': 'Ångra Utcheckning',
      'checkpoint.undoCheckIn': 'Ångra Incheckning',
      'checkpoint.finished': 'Slutförd',
      'geolocation.title': 'Platsåtkomst Krävs',
      'geolocation.message': 'Denna app kräver platsåtkomst för att spåra din framgång.',
      'geolocation.allowButton': 'Tillåt Platsåtkomst',
      'message.brevetAbandoned': 'Du har framgångsrikt övergivit brevet',
      'message.abandonUndone': 'Övergivandet av brevet har framgångsrikt ångrats',
      'message.abandonFailed': 'Misslyckades att överge brevet. Försök igen.',
      'message.undoFailed': 'Misslyckades att ångra övergivandet av brevet. Försök igen.',
      'message.checkpointsComingSoon': 'Kontrollpunkter för denna bana kommer snart.',
      'message.locationAccessGranted': 'Platsåtkomst beviljad',
      'message.locationAccessDenied': 'Platsåtkomst nekad',
      'connection.offline': 'Du är för närvarande offline',
      'connection.slowConnection': 'Långsam anslutning upptäckt - Laddning kan ta längre tid',
      'connection.loading': 'Laddar...',
      'connection.initializing': 'Initialiserar...',
      'connection.almostReady': 'Nästan klar...'
    },
    fr: {
      'common.loading': 'Chargement...',
      'common.error': 'Erreur',
      'common.success': 'Succès',
      'common.cancel': 'Annuler',
      'common.confirm': 'Confirmer',
      'common.yes': 'Oui',
      'common.no': 'Non',
      'login.title': 'Portail Concurrent',
      'login.subtitle': 'App Cycliste',
      'login.username': 'Nom d\'utilisateur',
      'login.password': 'Mot de passe',
      'login.loginButton': 'Se connecter',
      'login.error.invalidCredentials': 'Nom d\'utilisateur ou mot de passe invalide',
      'competitor.title': 'Tableau de Bord Concurrent',
      'competitor.startNumber': 'Numéro de Départ',
      'competitor.riderName': 'Nom du Cycliste',
      'competitor.trackInfo': 'Informations Piste',
      'competitor.checkpoints': 'Points de Contrôle',
      'competitor.abandonBrevet': 'Abandonner Brevet',
      'competitor.undoAbandon': 'Annuler Abandon',
      'competitor.abandonConfirm': 'Êtes-vous sûr de vouloir abandonner le brevet? Cette action ne peut pas être facilement annulée.',
      'competitor.undoAbandonConfirm': 'Êtes-vous sûr de vouloir annuler l\'abandon du brevet?',
      'checkpoint.distance': 'Distance',
      'checkpoint.toNext': 'Au suivant',
      'checkpoint.opens': 'Ouvre',
      'checkpoint.closes': 'Ferme',
      'checkpoint.service': 'Service',
      'checkpoint.time': 'Heure',
      'checkpoint.checkIn': 'Enregistrer',
      'checkpoint.checkOut': 'Départ',
      'checkpoint.checkedIn': 'Enregistré',
      'checkpoint.checkedOut': 'Parti',
      'checkpoint.undoCheckOut': 'Annuler Départ',
      'checkpoint.undoCheckIn': 'Annuler Enregistrement',
      'checkpoint.finished': 'Terminé',
      'geolocation.title': 'Accès Localisation Requis',
      'geolocation.message': 'Cette application nécessite l\'accès à la localisation pour suivre vos progrès.',
      'geolocation.allowButton': 'Autoriser Accès Localisation',
      'message.brevetAbandoned': 'Vous avez abandonné le brevet avec succès',
      'message.abandonUndone': 'L\'abandon du brevet a été annulé avec succès',
      'message.abandonFailed': 'Échec de l\'abandon du brevet. Veuillez réessayer.',
      'message.undoFailed': 'Échec de l\'annulation de l\'abandon du brevet. Veuillez réessayer.',
      'message.checkpointsComingSoon': 'Les points de contrôle pour cette piste seront bientôt disponibles.',
      'message.locationAccessGranted': 'Accès localisation accordé',
      'message.locationAccessDenied': 'Accès localisation refusé',
      'connection.offline': 'Vous êtes actuellement hors ligne',
      'connection.slowConnection': 'Connexion lente détectée - Le chargement peut prendre plus de temps',
      'connection.loading': 'Chargement...',
      'connection.initializing': 'Initialisation...',
      'connection.almostReady': 'Presque prêt...'
    },
    de: {
      'common.loading': 'Wird geladen...',
      'common.error': 'Fehler',
      'common.success': 'Erfolg',
      'common.cancel': 'Abbrechen',
      'common.confirm': 'Bestätigen',
      'common.yes': 'Ja',
      'common.no': 'Nein',
      'login.title': 'Teilnehmer Portal',
      'login.subtitle': 'Fahrer App',
      'login.username': 'Benutzername',
      'login.password': 'Passwort',
      'login.loginButton': 'Anmelden',
      'login.error.invalidCredentials': 'Ungültiger Benutzername oder Passwort',
      'competitor.title': 'Teilnehmer Dashboard',
      'competitor.startNumber': 'Startnummer',
      'competitor.riderName': 'Fahrer Name',
      'competitor.trackInfo': 'Streckeninformation',
      'competitor.checkpoints': 'Kontrollpunkte',
      'competitor.abandonBrevet': 'Brevet Aufgeben',
      'competitor.undoAbandon': 'Aufgeben Rückgängig',
      'competitor.abandonConfirm': 'Sind Sie sicher, dass Sie das Brevet aufgeben möchten? Diese Aktion kann nicht einfach rückgängig gemacht werden.',
      'competitor.undoAbandonConfirm': 'Sind Sie sicher, dass Sie das Aufgeben des Brevets rückgängig machen möchten?',
      'checkpoint.distance': 'Entfernung',
      'checkpoint.toNext': 'Zum nächsten',
      'checkpoint.opens': 'Öffnet',
      'checkpoint.closes': 'Schließt',
      'checkpoint.service': 'Service',
      'checkpoint.time': 'Zeit',
      'checkpoint.checkIn': 'Einchecken',
      'checkpoint.checkOut': 'Auschecken',
      'checkpoint.checkedIn': 'Eingecheckt',
      'checkpoint.checkedOut': 'Ausgecheckt',
      'checkpoint.undoCheckOut': 'Auschecken Rückgängig',
      'checkpoint.undoCheckIn': 'Einchecken Rückgängig',
      'checkpoint.finished': 'Beendet',
      'geolocation.title': 'Standortzugriff Erforderlich',
      'geolocation.message': 'Diese App benötigt Standortzugriff, um Ihren Fortschritt zu verfolgen.',
      'geolocation.allowButton': 'Standortzugriff Erlauben',
      'message.brevetAbandoned': 'Sie haben das Brevet erfolgreich aufgegeben',
      'message.abandonUndone': 'Das Aufgeben des Brevets wurde erfolgreich rückgängig gemacht',
      'message.abandonFailed': 'Fehler beim Aufgeben des Brevets. Bitte versuchen Sie es erneut.',
      'message.undoFailed': 'Fehler beim Rückgängigmachen des Brevet-Aufgebens. Bitte versuchen Sie es erneut.',
      'message.checkpointsComingSoon': 'Kontrollpunkte für diese Strecke werden bald verfügbar sein.',
      'message.locationAccessGranted': 'Standortzugriff gewährt',
      'message.locationAccessDenied': 'Standortzugriff verweigert',
      'connection.offline': 'Sie sind derzeit offline',
      'connection.slowConnection': 'Langsame Verbindung erkannt - Laden kann länger dauern',
      'connection.loading': 'Wird geladen...',
      'connection.initializing': 'Initialisierung...',
      'connection.almostReady': 'Fast fertig...'
    }
  };

  translate(key: keyof TranslationKeys): string {
    const currentLang = this.languageService.getCurrentLanguage();
    return this.translations[currentLang]?.[key] || key;
  }
}
