// Shared Components and Interfaces
// This file exports all the page layout related components and interfaces

// Page Header Component
export interface PageHeaderConfig {
  icon: string;
  title: string;
  description: string;
}

// Action Card Component
export interface ActionCardConfig {
  icon: string;
  title: string;
  description: string;
  isActive?: boolean;
  action?: string;
}

// Export the components (these will be imported from the shared module)
export { PageHeaderComponent } from './page-header/page-header.component';
export { ActionCardComponent } from './action-card/action-card.component';
export { PageLayoutComponent } from './page-layout/page-layout.component';
