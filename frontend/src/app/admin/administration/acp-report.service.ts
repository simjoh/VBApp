import {Injectable} from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {firstValueFrom, Observable} from 'rxjs';
import {environment} from "../../../environments/environment";
import {shareReplay, tap} from "rxjs/operators";
import {AcpReportRepresentation} from "../../shared/api/api";
import {LinkService} from "../../core/link.service";


@Injectable({
  providedIn: 'root'
})
export class AcpReportService {

  private baseUrl = environment.backend_url + '/administration/acpreport';

  constructor(private http: HttpClient, private linkservice: LinkService) {
  }

  /**
   * Create an ACP report for a specific track.
   * @param trackUid Unique identifier for the track.
   * @param reportData Data for the ACP report.
   */
  createAcpReport(trackUid: string, reportData: AcpReportRepresentation): Observable<AcpReportRepresentation> {
    return this.http.post<AcpReportRepresentation>(`${this.baseUrl}/report/track/${trackUid}`, reportData).pipe(
      tap(report => console.log("Created ACP Report", report)),
      shareReplay(1)
    );
  }

  /**
   * Delete an ACP report by its unique identifier.
   * @param reportUid Unique identifier for the report.
   */
  async deleteReport(reportUid: string) {
    return firstValueFrom(this.http.delete(`${this.baseUrl}/report/${reportUid}`));
  }

  /**
   * Approve an ACP report.
   * @param reportUid Unique identifier for the report.
   */
  approveReport(reportUid: string): Observable<AcpReportRepresentation> {
    return this.http.put<AcpReportRepresentation>(`${this.baseUrl}/approve/report/${reportUid}`, {}).pipe(
      tap(report => console.log("Approved ACP Report", report)),
      shareReplay(1)
    );
  }

  /**
   * Mark an ACP report as ready for approval.
   * @param reportUid Unique identifier for the report.
   */
  markAsReadyForApproval(reportUid: string): Observable<AcpReportRepresentation> {
    return this.http.put<AcpReportRepresentation>(`${this.baseUrl}/markreadyforapproval/report/${reportUid}`, {}).pipe(
      tap(report => console.log("Marked as Ready for Approval", report)),
      shareReplay(1)
    );
  }

}
