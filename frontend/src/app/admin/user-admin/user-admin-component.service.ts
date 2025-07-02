import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { environment } from 'src/environments/environment';
import { map } from "rxjs/operators";
import { User } from "../../shared/api/api";
import { Observable } from "rxjs";

interface AdminUser extends User {
  name: string;
  email: string;
  permissions: string;
  active: boolean;
}

@Injectable()
export class UserAdminComponentService {
  private apiUrl = environment.backend_url + "user/";

  constructor(private httpClient: HttpClient) {}

  getAllUsers(): Observable<AdminUser[]> {
    return this.httpClient.get<User[]>(environment.backend_url + 'users').pipe(
      map((users: User[]) => {
        return users.map(user => ({
          ...user,
          name: `${user.givenname} ${user.familyname}`,
          email: user.userInfoRepresentation?.email || '',
          permissions: user.roles?.length
            ? user.roles.map((r: any) => r.role_name ?? r).join(', ')
            : 'User',
          active: true // Temporary, should come from backend
        }));
      })
    );
  }

  createUser(user: Partial<AdminUser>): Observable<AdminUser> {
    return this.httpClient.post<AdminUser>(this.apiUrl, user);
  }

  updateUser(userId: string, user: Partial<AdminUser>): Observable<AdminUser> {
    return this.httpClient.put<AdminUser>(`${this.apiUrl}${userId}`, user);
  }

  deleteUser(userId: string): Observable<void> {
    return this.httpClient.delete<void>(`${this.apiUrl}${userId}`);
  }
}
