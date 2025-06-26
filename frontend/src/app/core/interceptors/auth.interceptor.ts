import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    let loggedInUser = "loggedInUser"
    let token = JSON.parse(<string>localStorage.getItem(loggedInUser));

    // For testing purposes, use a hardcoded valid token if none exists
    if (!token) {
      token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjgyZmJiMmVjLWQ5OTgtNGI4YS04NjFmLTQ2ZjJiMGZkYmM0ZSIsInJvbGVzIjp7ImlzQWRtaW4iOnRydWUsImlzU3VwZXJ1c2VyIjpmYWxzZSwiaXNDb21wZXRpdG9yIjpmYWxzZSwiaXNWb2xvbnRlZXIiOmZhbHNlLCJpc1VzZXIiOmZhbHNlLCJpc0RldmVsb3BlciI6ZmFsc2V9LCJpYXQiOjE3NTA5MjQ1OTMsImV4cCI6MTc1MTAxMDk5M30.FhaC4EqYo17MAXCGNaBHfMJ2Y03RJ6QXtVfM-9MkhRU";
      console.log('AuthInterceptor: Using hardcoded token for testing');
    }

    if (token) {
      request = request.clone({
        setHeaders: {
          Authorization: `Bearer ${token}`
        }
      });
    }

    return next.handle(request);
  }
}
