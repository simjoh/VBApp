import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { MessageService } from '../../core/services/message.service';
import { TranslationService } from '../../core/services/translation.service';
import { TranslationPipe } from '../../shared/pipes/translation.pipe';
import { LogoComponent } from '../../shared/components/logo/logo.component';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, LogoComponent, TranslationPipe],
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss'
})
export class LoginComponent {
  private fb = new FormBuilder();
  private router = inject(Router);
  private authService = inject(AuthService);
  private messageService = inject(MessageService);
  private translationService = inject(TranslationService);

  loginForm: FormGroup;
  loginError: string | null = null;

  constructor() {
    this.loginForm = this.fb.group({
      username: ['', Validators.required],
      password: ['', Validators.required]
    });
  }

  onSubmit() {
    console.log('Login form submitted');
    console.log('Form valid:', this.loginForm.valid);
    console.log('Form value:', this.loginForm.value);

    // Clear any previous error
    this.loginError = null;

    if (this.loginForm.valid) {
      const loginModel = this.loginForm.value;
      console.log('Calling authService.login with:', loginModel);

      this.authService.login(loginModel).subscribe({
        next: (success) => {
          console.log('Login result:', success);
          if (success) {

            this.router.navigate(['/']);
          } else {
            this.loginError = 'Access denied. Only competitors can access this application.';
          }
        },
        error: (error) => {
          console.error('Login subscription error:', error);
          this.loginError = 'Login failed. Please check your credentials and try again.';
        }
      });
    } else {
      console.log('Form is invalid');
      this.loginError = 'Please fill in all required fields.';
    }
  }
}
