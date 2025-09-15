import { Component, OnInit, OnDestroy, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { BehaviorSubject, Observable, combineLatest, of, Subscription } from 'rxjs';
import { map, startWith, switchMap, tap } from 'rxjs/operators';
import { MessageService } from 'primeng/api';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';
import { TrackRepresentation, ParticipantInformationRepresentation } from '../../../shared/api/api';
import { ParticipantComponentService } from '../participant-component.service';
import { TrackService } from '../../../shared/track-service';
import { ParticipantService } from '../../../shared/participant.service';

export interface MoveParticipantsConfig {
  mode: 'single' | 'bulk';
  participant?: ParticipantInformationRepresentation;
  currentTrackUid?: string;
}

export interface MoveResult {
  success: Array<{ participant_uid: string; startnumber: string }>;
  failed: Array<{ participant_uid: string; reason: string; links?: any[] }>;
  skipped: Array<{ participant_uid: string; reason: string }>;
}

@Component({
  selector: 'brevet-move-participants-dialog',
  templateUrl: './move-participants-dialog.component.html',
  styleUrls: ['./move-participants-dialog.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [ParticipantComponentService, TrackService, ParticipantService]
})
export class MoveParticipantsDialogComponent implements OnInit, OnDestroy {

  moveForm: FormGroup;
  loading = false;
  resolvingConflicts = false;

  // Observables for reactive data
  $tracks: Observable<TrackRepresentation[]>;
  $selectedFromTrack: Observable<TrackRepresentation | null>;
  $selectedToTrack: Observable<TrackRepresentation | null>;
  $participantsOnFromTrack: Observable<ParticipantInformationRepresentation[]>;
  $selectedParticipants: Observable<ParticipantInformationRepresentation[]>;
  $canMove: Observable<boolean>;
  $moveResult: Observable<MoveResult | null>;

  // Subjects for state management
  private selectedFromTrackSubject = new BehaviorSubject<string | null>(null);
  private selectedToTrackSubject = new BehaviorSubject<string | null>(null);
  private selectedParticipantsSubject = new BehaviorSubject<ParticipantInformationRepresentation[]>([]);
  private moveResultSubject = new BehaviorSubject<MoveResult | null>(null);

  // Configuration
  dialogConfig: MoveParticipantsConfig;
  selectedParticipants: ParticipantInformationRepresentation[] = [];
  currentParticipants: ParticipantInformationRepresentation[] = [];
  private lastFromTrackUid: string | null = null;
  showResolveButtons = false; // default to auto-resolve, hide buttons
  private autoResolvingBulk = false;
  private resolvingPerParticipant = new Set<string>();
  private subscriptions: Subscription[] = [];

  constructor(
    public ref: DynamicDialogRef,
    public config: DynamicDialogConfig,
    private fb: FormBuilder,
    private http: HttpClient,
    private messageService: MessageService,
    private participantComponentService: ParticipantComponentService,
    private participantService: ParticipantService,
    private cdr: ChangeDetectorRef
  ) {
    this.dialogConfig = config.data;
    this.initializeForm();
    this.initializeObservables();
  }

  ngOnDestroy(): void {
    // Clean up all subscriptions to prevent memory leaks
    this.subscriptions.forEach(sub => sub.unsubscribe());
  }

  private async autoDiagnoseConflictsAfterBulk(fromUid: string, toUid: string): Promise<void> {
    try {
      if (!fromUid || !toUid) return;

      const remaining = await this.participantService.participantsForTrackExtended(fromUid).toPromise();
      const stillMovable = (remaining || []).filter(p => this.canMoveParticipant(p));
      if (stillMovable.length === 0) return;

      const failed: Array<{ participant_uid: string; reason: string; links?: any[] }> = [];
      for (const p of stillMovable) {
        try {
          const url = `${environment.backend_url}participant/${p.participant.participant_uid}/move`;
          await this.http.put(url, { new_track_uid: toUid }, { headers: { 'X-Ignore-Errors': 'true' } }).toPromise();
        } catch (err: any) {
          failed.push(this.extractFailureFromError(err, p.participant.participant_uid, toUid));
        }
      }

      if (failed.length > 0) {
        const current = this.moveResultSubject.getValue() || { success: [], failed: [], skipped: [] } as MoveResult;
        const merged: MoveResult = { success: current.success || [], skipped: current.skipped || [], failed: [...(current.failed || []), ...failed] };
        this.moveResultSubject.next(merged);
      }
    } catch (e) {
      console.error('Error during auto-diagnose after bulk:', e);
    }
  }

  ngOnInit(): void {
    this.setupInitialState();

    // Trigger initial form values
    if (this.dialogConfig.currentTrackUid) {
      this.selectedFromTrackSubject.next(this.dialogConfig.currentTrackUid);
      this.lastFromTrackUid = this.dialogConfig.currentTrackUid;
    }

    // Debug logging
    console.log('Dialog config:', this.dialogConfig);
    console.log('Tracks observable:', this.$tracks);

    // Subscribe to participants observable for debugging
    this.subscriptions.push(
      this.$participantsOnFromTrack.subscribe(participants => {
        console.log('Participants loaded:', participants);
        this.currentParticipants = participants;
        if (participants.length > 0) {
          console.log('First participant structure:', participants[0]);
          console.log('First participant UID:', participants[0].participant?.participant_uid);
        }
      })
    );

    // Subscribe to selected participants observable for debugging
    this.subscriptions.push(
      this.$selectedParticipants.subscribe(participants => {
        console.log('Selected participants observable updated:', participants);
      })
    );

    // Subscribe to can move observable for debugging
    this.subscriptions.push(
      this.$canMove.subscribe(canMove => {
        console.log('Can move:', canMove);
        console.log('Selected participants count:', this.selectedParticipants.length);
        console.log('Selected participants:', this.selectedParticipants);
      })
    );

    // Subscribe to form value changes for debugging
    this.subscriptions.push(
      this.moveForm.valueChanges.subscribe(values => {
        console.log('Form values:', values);
        console.log('Form valid:', this.moveForm.valid);
      })
    );
  }

  private initializeForm(): void {
    this.moveForm = this.fb.group({
      fromTrackUid: [this.dialogConfig.currentTrackUid || null, this.dialogConfig.mode === 'bulk' ? Validators.required : []],
      toTrackUid: [null, Validators.required],
      selectedParticipants: [this.dialogConfig.mode === 'single' ? [this.dialogConfig.participant] : [], []]
    });

    // Set up form value changes
    this.subscriptions.push(
      this.moveForm.get('fromTrackUid')?.valueChanges.subscribe(trackUid => {
        this.selectedFromTrackSubject.next(trackUid);
        this.lastFromTrackUid = trackUid;
      }) || new Subscription()
    );

    this.subscriptions.push(
      this.moveForm.get('toTrackUid')?.valueChanges.subscribe(trackUid => {
        this.selectedToTrackSubject.next(trackUid);
      }) || new Subscription()
    );
  }

  private initializeObservables(): void {
    // Get all available tracks
    this.$tracks = this.participantComponentService.tracks$;

    // Selected from track
    this.$selectedFromTrack = this.selectedFromTrackSubject.pipe(
      switchMap(trackUid =>
        trackUid ? this.participantComponentService.tracks$.pipe(
          map(tracks => tracks.find(t => t.track_uid === trackUid) || null)
        ) : of(null)
      )
    );

    // Selected to track
    this.$selectedToTrack = this.selectedToTrackSubject.pipe(
      switchMap(trackUid =>
        trackUid ? this.participantComponentService.tracks$.pipe(
          map(tracks => tracks.find(t => t.track_uid === trackUid) || null)
        ) : of(null)
      )
    );

    // Participants on from track
    this.$participantsOnFromTrack = this.selectedFromTrackSubject.pipe(
      switchMap(trackUid =>
        trackUid ? this.participantService.participantsForTrackExtended(trackUid) : of([])
      )
    );

    // Selected participants observable
    this.$selectedParticipants = this.selectedParticipantsSubject.asObservable();

        // Can move validation
    this.$canMove = combineLatest([
      this.$selectedFromTrack,
      this.$selectedToTrack,
      this.$participantsOnFromTrack
    ]).pipe(
      map(([fromTrack, toTrack, participants]) => {
        console.log('Validation check:', { fromTrack, toTrack, participantsCount: participants?.length, mode: this.dialogConfig.mode });

        if (this.dialogConfig.mode === 'single') {
          // For single mode, only need to select a target track
          const canMove = toTrack && fromTrack && fromTrack.track_uid !== toTrack.track_uid;
          console.log('Single mode can move:', canMove);
          return canMove;
        } else {
          // For bulk mode, need to have participants that can be moved
          const movableParticipants = participants?.filter(p => this.canMoveParticipant(p)) || [];
          const canMove = fromTrack && toTrack &&
                 fromTrack.track_uid !== toTrack.track_uid &&
                 movableParticipants.length > 0;
          console.log('Bulk mode can move:', canMove, 'movable participants:', movableParticipants.length);
          return canMove;
        }
      })
    );

    // Move result
    this.$moveResult = this.moveResultSubject.asObservable();
  }

  getFriendlyReason(reason: string | undefined | null): string {
    if (!reason) return 'Okänt fel';
    const r = String(reason).toLowerCase();
    if (r.includes('start number') || r.includes('already exists')) {
      return 'Startnummer är upptaget på målbana';
    }
    if (r.includes('already on the target track')) {
      return 'Deltagaren ligger redan på målbana';
    }
    if (r.includes('cannot move participant') && (r.includes('started') || r.includes('finished') || r.includes('dnf') || r.includes('dns'))) {
      return 'Deltagaren kan inte flyttas eftersom hen har startat/slutfört eller markerats DNF/DNS';
    }
    if (r.includes('participant not found')) {
      return 'Deltagaren hittades inte';
    }
    if (r.includes('target track not found')) {
      return 'Målbana hittades inte';
    }
    if (r.includes('source track not found')) {
      return 'Källbana hittades inte';
    }
    if (r.includes('source and target tracks are the same')) {
      return 'Käll- och målbana är samma';
    }
    if (r.includes('no participants found on source track')) {
      return 'Det finns inga deltagare på källbanan';
    }
    if (r.includes('failed to move participant checkpoints')) {
      return 'Tekniskt fel vid flytt av kontrollpunkter';
    }
    return reason as string;
  }

  private setupInitialState(): void {
    if (this.dialogConfig.mode === 'single' && this.dialogConfig.participant) {
      this.selectedParticipants = [this.dialogConfig.participant];
      this.selectedParticipantsSubject.next([this.dialogConfig.participant]);
      this.moveForm.patchValue({
        selectedParticipants: [this.dialogConfig.participant]
      });
    }
  }

  onFromTrackChange(trackUid: string): void {
    this.selectedFromTrackSubject.next(trackUid);
    this.lastFromTrackUid = trackUid;
    this.selectedParticipants = [];
    this.selectedParticipantsSubject.next([]);
    this.moveForm.patchValue({ selectedParticipants: [] });
  }

  onToTrackChange(trackUid: string): void {
    console.log('To track changed:', trackUid);
    this.selectedToTrackSubject.next(trackUid);
  }

  isParticipantSelected(participant: ParticipantInformationRepresentation): boolean {
    const isSelected = this.selectedParticipants.some(p => p.participant.participant_uid === participant.participant.participant_uid);
    console.log(`Checking if participant ${participant.participant.participant_uid} is selected:`, isSelected);
    console.log('Current selectedParticipants:', this.selectedParticipants.map(p => p.participant.participant_uid));
    return isSelected;
  }

    isAllSelected(): boolean {
    const selectableParticipants = this.currentParticipants.filter(p => this.canMoveParticipant(p));
    return selectableParticipants.length > 0 &&
           selectableParticipants.every(p => this.isParticipantSelected(p));
  }

  toggleParticipantSelection(participant: ParticipantInformationRepresentation, event: any): void {
    console.log('Toggle participant selection:', participant.participant.participant_uid, event.checked);

    if (event.checked) {
      if (!this.isParticipantSelected(participant)) {
        this.selectedParticipants.push(participant);
      }
    } else {
      this.selectedParticipants = this.selectedParticipants.filter(
        p => p.participant.participant_uid !== participant.participant.participant_uid
      );
    }

    console.log('Selected participants after toggle:', this.selectedParticipants);
    this.selectedParticipantsSubject.next(this.selectedParticipants);
    this.moveForm.patchValue({ selectedParticipants: this.selectedParticipants });
    this.cdr.detectChanges();
  }

      toggleSelectAll(): void {
    console.log('Toggle select all called');
    console.log('Current participants:', this.currentParticipants);

    const selectableParticipants = this.currentParticipants.filter(p => this.canMoveParticipant(p));
    console.log('Selectable participants:', selectableParticipants);

    // Check if all are currently selected
    const allSelected = selectableParticipants.every(p => this.isParticipantSelected(p));

    if (allSelected) {
      // Deselect all
      this.selectedParticipants = [];
      console.log('Deselecting all participants');
    } else {
      // Select all selectable participants
      this.selectedParticipants = [...selectableParticipants];
      console.log('Selecting all participants');
    }

    console.log('Selected participants after toggle:', this.selectedParticipants);
    this.selectedParticipantsSubject.next(this.selectedParticipants);
    this.moveForm.patchValue({ selectedParticipants: this.selectedParticipants });
    this.cdr.detectChanges();
  }

  canMoveParticipant(participant: ParticipantInformationRepresentation): boolean {
    return !participant.participant.started &&
           !participant.participant.finished &&
           !participant.participant.dnf &&
           !participant.participant.dns;
  }

  getMovableParticipantsCount(participants: ParticipantInformationRepresentation[]): number {
    return participants.filter(p => this.canMoveParticipant(p)).length;
  }

  getParticipantStatusText(participant: ParticipantInformationRepresentation): string {
    if (participant.participant.started) return 'Har startat';
    if (participant.participant.finished) return 'Har slutfört';
    if (participant.participant.dnf) return 'DNF';
    if (participant.participant.dns) return 'DNS';
    return 'Kan flyttas';
  }

  getParticipantStatusSeverity(participant: ParticipantInformationRepresentation): string {
    if (participant.participant.started || participant.participant.finished) return 'warning';
    if (participant.participant.dnf || participant.participant.dns) return 'danger';
    return 'success';
  }

  async moveParticipants(): Promise<void> {
    if (this.moveForm.invalid || this.loading) return;

    this.loading = true;
    const formValue = this.moveForm.value;

    try {
      if (this.dialogConfig.mode === 'single') {
        await this.moveSingleParticipant(formValue);
      } else {
        await this.moveBulkParticipants(formValue);
      }
    } catch (error) {
      console.error('Error moving participants:', error);
      this.messageService.add({
        severity: 'error',
        summary: 'Fel',
        detail: 'Ett fel uppstod vid flytt av deltagare'
      });
    } finally {
      this.loading = false;
    }
  }

  private async moveSingleParticipant(formValue: any, attemptedResolve: boolean = false): Promise<void> {
    try {
      const participant = this.selectedParticipants[0];
      const url = `${environment.backend_url}participant/${participant.participant.participant_uid}/move`;

      console.log('Sending single move request:', {
        participant_uid: participant.participant.participant_uid,
        new_track_uid: formValue.toTrackUid
      });
      console.log('Request URL:', url);

      const response = await this.http.put(url, {
        new_track_uid: formValue.toTrackUid
      }).toPromise();

      console.log('Single move response:', response);

      this.messageService.add({
        severity: 'success',
        summary: 'Framgång',
        detail: 'Deltagare flyttad framgångsrikt'
      });

      this.ref.close({ success: true, participant });
    } catch (error) {
      // Handle 400 (likely start number conflict) quietly; log others as errors
      if ((error as any).status !== 400) {
        console.error('Error in single move:', error);
      } else {
        console.warn('Single move returned 400. Attempting conflict resolution...', error);
      }

      // Check if it's a validation/conflict error
      if ((error as any).status === 400) {
        const errBody: any = (error as any).error || {};
        console.log('Single move 400 response body:', errBody);

        // If backend returned MoveParticipantResponseRepresentation with failed/links
        if (errBody && (errBody.failed || errBody.success || errBody.skipped)) {
          const moveResult: MoveResult = {
            success: errBody.success || [],
            failed: errBody.failed || [],
            skipped: errBody.skipped || []
          };
          this.moveResultSubject.next(moveResult);

          // Auto-handle start number conflict if a resolve link is provided
          const firstFailed = Array.isArray(errBody.failed) ? errBody.failed[0] : null;
          const hasResolveLink = !!(firstFailed && firstFailed.links);
          if (hasResolveLink) {
            try {
              const selected = this.selectedParticipants[0];
              await this.resolveConflict(selected.participant.participant_uid, formValue.toTrackUid);
              return; // resolveConflict will re-run the move and close on success
            } catch (e) {
              console.error('Auto-resolve failed:', e);
            }
          }
        }

        // Fallback: if response body is empty or lacks details, synthesize a conflict entry with a resolve link
        if (!errBody || (!errBody.failed && !errBody.success && !errBody.skipped)) {
          const selected = this.selectedParticipants[0];
          const synthetic: MoveResult = {
            success: [],
            failed: [
              {
                participant_uid: selected?.participant?.participant_uid,
                reason: 'Startnummerkonflikt eller valideringsfel',
                links: [
                  {
                    rel: 'resolveStartnumberConflict',
                    method: 'POST',
                    href: `${environment.backend_url}participant/${selected?.participant?.participant_uid}/resolve-startnumber-conflict`
                  }
                ]
              }
            ],
            skipped: []
          };
          this.moveResultSubject.next(synthetic);
        }

        // Then try resolving once optimistically
        if (!attemptedResolve) {
          try {
            const selected = this.selectedParticipants[0];
            await this.resolveConflict(selected.participant.participant_uid, formValue.toTrackUid);
            // After resolving, retry the move once
            await this.moveSingleParticipant(formValue, true);
            return;
          } catch (e) {
            console.error('Optimistic resolve failed:', e);
          }
        }

        const detailMsg = errBody?.failed?.[0]?.reason || 'Deltagaren kan inte flyttas.';
        this.messageService.add({
          severity: 'warn',
          summary: 'Kan inte flytta',
          detail: detailMsg
        });
      } else {
        this.messageService.add({
          severity: 'error',
          summary: 'Fel',
          detail: 'Ett fel uppstod vid flytt av deltagare'
        });
      }
    }
  }

    private async moveBulkParticipants(formValue: any): Promise<void> {
    const url = `${environment.backend_url}participants/move`;

    try {
      // Get all movable participants from the current track
      const movableParticipants = this.currentParticipants.filter(p => this.canMoveParticipant(p));

      const effectiveFromUid = this.lastFromTrackUid || formValue.fromTrackUid;
      const requestPayload = {
        from_track_uid: effectiveFromUid,
        to_track_uid: formValue.toTrackUid
      } as { from_track_uid: string | null; to_track_uid: string };

      console.log('Sending bulk move request:', requestPayload);
      console.log('Request URL:', url);
      console.log('Moving participants:', movableParticipants.length);

      if (!requestPayload.from_track_uid) {
        this.messageService.add({ severity: 'warn', summary: 'Saknar källa', detail: 'Välj en källbana först' });
        return;
      }
      const response = await this.http.put(url, requestPayload, { headers: { 'X-Ignore-Errors': 'true' } }).toPromise();

      console.log('Bulk move response:', response);

      // Check if response has the expected structure
      if (response && typeof response === 'object') {
        console.log('Response keys:', Object.keys(response));

        // Try to access the properties directly
        const success = (response as any).success || [];
        const failed = (response as any).failed || [];
        const skipped = (response as any).skipped || [];

        console.log('Extracted properties:', { success, failed, skipped });

        const moveResult: MoveResult = {
          success: success,
          failed: failed,
          skipped: skipped
        };

        this.moveResultSubject.next(moveResult);

        // Auto-resolve any startnumber conflicts in bulk mode
        const hasResolvable = Array.isArray(moveResult.failed) && moveResult.failed.some((f: any) => !!f.links);
        if (hasResolvable && !this.autoResolvingBulk) {
          this.autoResolvingBulk = true;
          await this.resolveAllConflicts();
          this.autoResolvingBulk = false;
          return; // resolveAllConflicts will rerun the move
        }

        // Show summary message
        const totalMoved = moveResult.success.length;
        const totalFailed = moveResult.failed.length;
        const totalSkipped = moveResult.skipped.length;

        if (totalMoved > 0) {
          this.messageService.add({
            severity: 'success',
            summary: 'Framgång',
            detail: `${totalMoved} deltagare flyttade framgångsrikt`
          });
        }

        if (totalFailed > 0 || totalSkipped > 0) {
          this.messageService.add({
            severity: 'warn',
            summary: 'Varning',
            detail: `${totalFailed} misslyckades, ${totalSkipped} hoppades över`
          });
        }

        // Auto-diagnose to extract resolvable conflict links if missing
        await this.autoDiagnoseConflictsAfterBulk((this.lastFromTrackUid || formValue.fromTrackUid)!, formValue.toTrackUid);

        // Refresh participants list for the source track inside the dialog
        try {
          const fromUid = this.lastFromTrackUid || formValue.fromTrackUid;
          if (fromUid) {
            // Re-trigger fetch via subject to refresh reactive streams and table
            this.selectedFromTrackSubject.next(null);
            this.selectedFromTrackSubject.next(fromUid);

            // Fallback: if backend did not return success items, infer moved participants by diff
            const latest = await this.participantService.participantsForTrackExtended(fromUid).toPromise();
            const latestMovable = (latest || []).filter(p => this.canMoveParticipant(p));

            const originallyMovableUids = new Set(movableParticipants.map(p => p.participant.participant_uid));
            const stillMovableUids = new Set(latestMovable.map(p => p.participant.participant_uid));
            const movedUids = Array.from(originallyMovableUids).filter(uid => !stillMovableUids.has(uid));

            if ((success?.length ?? 0) === 0 && movedUids.length > 0) {
              const inferredSuccess = movedUids.map(uid => {
                const found = movableParticipants.find(p => p.participant.participant_uid === uid);
                return { participant_uid: uid, startnumber: found?.participant?.startnumber as any };
              });

              const current = this.moveResultSubject.getValue() || { success: [], failed: [], skipped: [] } as MoveResult;
              const merged: MoveResult = {
                success: [...(current.success || []), ...inferredSuccess],
                failed: current.failed || [],
                skipped: current.skipped || []
              };
              this.moveResultSubject.next(merged);

              // Also show a success toast if we did not show earlier
              this.messageService.add({
                severity: 'success',
                summary: 'Framgång',
                detail: `${inferredSuccess.length} deltagare flyttade framgångsrikt`
              });
            }
          }
        } catch (refreshError) {
          console.error('Failed to refresh participants after bulk move:', refreshError);
        }
      } else {
        // Handle unexpected response format
        console.error('Unexpected response format:', response);
        this.messageService.add({
          severity: 'error',
          summary: 'Fel',
          detail: 'Oväntat svar från servern'
        });
      }
    } catch (error) {
      console.error('Error in bulk move:', error);
      // Try to parse structured error payload (like assembler output)
      const errBody: any = (error as any)?.error || {};
      if (errBody && (errBody.failed || errBody.success || errBody.skipped)) {
        const moveResult: MoveResult = {
          success: errBody.success || [],
          failed: errBody.failed || [],
          skipped: errBody.skipped || []
        };
        this.moveResultSubject.next(moveResult);
        const hasResolvable = Array.isArray(moveResult.failed) && moveResult.failed.some((f: any) => !!f.links);
        if (hasResolvable && !this.autoResolvingBulk) {
          this.autoResolvingBulk = true;
          await this.resolveAllConflicts();
          this.autoResolvingBulk = false;
          return;
        }
        const totalFailed = moveResult.failed.length;
        const totalSkipped = moveResult.skipped.length;
        const detailMsg = `${totalFailed} misslyckades, ${totalSkipped} hoppades över`;
        this.messageService.add({
          severity: 'warn',
          summary: 'Vissa kunde inte flyttas',
          detail: detailMsg
        });

        // Also try to extract per-participant conflict details
        await this.autoDiagnoseConflictsAfterBulk((this.lastFromTrackUid || formValue.fromTrackUid)!, formValue.toTrackUid);
      } else {
        this.messageService.add({
          severity: 'error',
          summary: 'Fel',
          detail: 'Ett fel uppstod vid flytt av deltagare'
        });
      }
    }
  }

  async resolveConflict(participantUid: string, toTrackUid: string): Promise<void> {
    this.resolvingConflicts = true;

    try {
      const url = `${environment.backend_url}participant/${participantUid}/resolve-startnumber-conflict`;

      await this.http.post(url, {
        to_track_uid: toTrackUid
      }).toPromise();

      this.messageService.add({
        severity: 'success',
        summary: 'Konflikt löst',
        detail: 'Startnummer konflikt löst automatiskt'
      });

      if (this.dialogConfig.mode === 'single') {
        // Resolve endpoint already moves the participant with a new startnumber
        this.ref.close({ success: true, reload: true });
      } else {
        // For bulk, re-run the move after resolving
        // Refresh list quickly to reflect disappearance even before bulk rerun completes
        const fromUid = this.lastFromTrackUid || this.moveForm.get('fromTrackUid')?.value;
        if (fromUid) {
          this.selectedFromTrackSubject.next(null);
          this.selectedFromTrackSubject.next(fromUid);
        }
        this.moveParticipants();
      }

    } catch (error) {
      console.error('Error resolving conflict:', error);
      this.messageService.add({
        severity: 'error',
        summary: 'Fel',
        detail: 'Kunde inte lösa startnummer konflikt'
      });
    } finally {
      this.resolvingConflicts = false;
    }
  }

  async resolveAllConflicts(): Promise<void> {
    this.resolvingConflicts = true;
    try {
      const currentResult = this.moveResultSubject.getValue();
      const toTrackUid = this.moveForm.get('toTrackUid')?.value;
      if (!currentResult || !toTrackUid) {
        this.resolvingConflicts = false;
        return;
      }

      // Prefer failed items; if no failed present with identifiable UIDs, fall back to all still movable on source track
      const failedItems = Array.isArray(currentResult.failed) ? currentResult.failed : [];
      let uidsToResolve: string[] = failedItems
        .map(f => f?.participant_uid)
        .filter((uid): uid is string => typeof uid === 'string' && uid.length > 0);

      if (uidsToResolve.length === 0) {
        // Fallback: derive from current participants that are still movable
        const remainingMovable = this.currentParticipants.filter(p => this.canMoveParticipant(p));
        uidsToResolve = remainingMovable.map(p => p.participant.participant_uid);
      }

      if (uidsToResolve.length === 0) {
        this.resolvingConflicts = false;
        return;
      }

      await Promise.all(uidsToResolve.map(uid => {
        const url = `${environment.backend_url}participant/${uid}/resolve-startnumber-conflict`;
        return this.http.post(url, { to_track_uid: toTrackUid }, { headers: { 'X-Ignore-Errors': 'true' } }).toPromise();
      }));

      this.messageService.add({
        severity: 'success',
        summary: 'Konflikter lösta',
        detail: `${uidsToResolve.length} konflikter har lösts`
      });

      // Refresh the source track list so resolved (moved) participants disappear
      const fromUid = this.lastFromTrackUid || this.moveForm.get('fromTrackUid')?.value;
      if (fromUid) {
        this.selectedFromTrackSubject.next(null);
        this.selectedFromTrackSubject.next(fromUid);
      }

      // Clear the failed list for resolved participants so buttons/columns hide
      const current = this.moveResultSubject.getValue();
      if (current) {
        const remainingFailed = (current.failed || []).filter(f => !uidsToResolve.includes(f.participant_uid));
        const updated: MoveResult = {
          success: current.success || [],
          skipped: current.skipped || [],
          failed: remainingFailed
        };
        this.moveResultSubject.next(updated);
      }
    } catch (error) {
      console.error('Error resolving all conflicts:', error);
      this.messageService.add({
        severity: 'error',
        summary: 'Fel',
        detail: 'Kunde inte lösa alla konflikter'
      });
    } finally {
      this.resolvingConflicts = false;
    }
  }

  async resolveFailed(participantUid: string): Promise<void> {
    const toTrackUid = this.moveForm.get('toTrackUid')?.value;
    if (!toTrackUid || !participantUid) return;
    this.resolvingPerParticipant.add(participantUid);
    try {
      await this.resolveConflict(participantUid, toTrackUid);
      // Optimistically drop from failed list to hide button immediately
      const current = this.moveResultSubject.getValue();
      if (current && Array.isArray(current.failed)) {
        const updated: MoveResult = {
          success: current.success || [],
          skipped: current.skipped || [],
          failed: current.failed.filter(f => f.participant_uid !== participantUid)
        };
        this.moveResultSubject.next(updated);
      }
    } finally {
      this.resolvingPerParticipant.delete(participantUid);
      this.cdr.detectChanges();
    }
  }

  hasResolvableConflicts(result: MoveResult | null): boolean {
    if (!result || !result.failed) {
      return false;
    }
    return result.failed.some((f: any) => !!f.links);
  }

  isResolving(participantUid: string): boolean {
    return this.resolvingPerParticipant.has(participantUid);
  }

  hasResolvableConflictForParticipant(participantUid: string): boolean {
    const result = this.moveResultSubject.getValue();
    if (!result || !Array.isArray(result.failed)) {
      return false;
    }
    return result.failed.some((f: any) => f.participant_uid === participantUid && !!f.links);
  }

  shouldShowResolveButtonForParticipant(participantUid: string): boolean {
    const result = this.moveResultSubject.getValue();
    if (!result || !Array.isArray(result.failed)) {
      return false;
    }
    return result.failed.some((f: any) => f.participant_uid === participantUid);
  }

  async diagnoseConflictsSequential(): Promise<void> {
    const toTrackUid = this.moveForm.get('toTrackUid')?.value;
    if (!toTrackUid) {
      return;
    }

    this.resolvingConflicts = true;
    try {
      const movable = this.currentParticipants.filter(p => this.canMoveParticipant(p));
      const success: Array<{ participant_uid: string; startnumber: string }> = [];
      const failed: Array<{ participant_uid: string; reason: string; links?: any[] }> = [];
      const skipped: Array<{ participant_uid: string; reason: string }> = [];

      for (const p of movable) {
        try {
          const url = `${environment.backend_url}participant/${p.participant.participant_uid}/move`;
          await this.http.put(url, { new_track_uid: toTrackUid }, { headers: { 'X-Ignore-Errors': 'true' } }).toPromise();
          success.push({ participant_uid: p.participant.participant_uid, startnumber: p.participant.startnumber as any });
        } catch (err: any) {
          failed.push(this.extractFailureFromError(err, p.participant.participant_uid, toTrackUid));
        }
      }

      const result: MoveResult = { success, failed, skipped };
      this.moveResultSubject.next(result);

      // Refresh source track list
          const fromUid = this.lastFromTrackUid || this.moveForm.get('fromTrackUid')?.value;
      if (fromUid) {
        this.selectedFromTrackSubject.next(null);
        this.selectedFromTrackSubject.next(fromUid);
      }

      if (success.length > 0) {
        this.messageService.add({ severity: 'success', summary: 'Framgång', detail: `${success.length} deltagare flyttades` });
      }
      if (failed.length > 0) {
        this.messageService.add({ severity: 'warn', summary: 'Konflikter', detail: `${failed.length} konflikter hittades` });
      }
    } finally {
      this.resolvingConflicts = false;
    }
  }

  private extractFailureFromError(error: any, participantUid: string, toTrackUid?: string): { participant_uid: string; reason: string; links?: any[] } {
    try {
      const body = error?.error;
      // If backend returned the assembler response
      if (body && typeof body === 'object') {
        if (Array.isArray(body.failed) && body.failed.length > 0) {
          return body.failed[0];
        }
        if (typeof body.error === 'string' && body.error.trim().length > 0) {
          return { participant_uid: participantUid, reason: body.error };
        }
      }
      // If backend returned text
      if (typeof body === 'string' && body.trim().length > 0) {
        try {
          const parsed = JSON.parse(body);
          if (parsed?.error) {
            return { participant_uid: participantUid, reason: parsed.error };
          }
        } catch (_) {
          return { participant_uid: participantUid, reason: body };
        }
      }
      // Fallback to status + statusText
      const statusText = error?.statusText || 'Okänt fel';
      const status = error?.status ? ` (${error.status})` : '';
      const base = { participant_uid: participantUid, reason: `${statusText}${status}` } as { participant_uid: string; reason: string; links?: any[] };
      // If 400 and we are in bulk context, surface a resolvable link to allow user to fix startnumber conflicts
      if (error?.status === 400 && toTrackUid) {
        base.reason = 'Startnummerkonflikt eller valideringsfel';
        base.links = [
          {
            rel: 'resolveStartnumberConflict',
            method: 'POST',
            href: `${environment.backend_url}participant/${participantUid}/resolve-startnumber-conflict`
          }
        ];
      }
      return base;
    } catch {
      return { participant_uid: participantUid, reason: 'Okänt fel' };
    }
  }

  close(): void {
    this.ref.close();
  }

  confirmAndClose(): void {
    this.ref.close({ success: true, reload: true });
  }
}
