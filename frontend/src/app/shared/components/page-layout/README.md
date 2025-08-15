# Page Layout Components

This directory contains reusable components for creating consistent page layouts across the application.

## Components

### PageHeaderComponent
A simple header component with an icon, title, and description.

```typescript
import { PageHeaderConfig } from '../../../shared/components';

headerConfig: PageHeaderConfig = {
  icon: 'pi pi-users',
  title: 'Hantera Deltagare',
  description: 'Hantera deltagare, registreringar och resultat för evenemang'
};
```

### ActionCardComponent
A clickable card component for navigation actions.

```typescript
import { ActionCardConfig } from '../../../shared/components';

actionCards: ActionCardConfig[] = [
  {
    icon: 'pi pi-list',
    title: 'Deltagarlista',
    description: 'Visa och hantera alla deltagare',
    action: 'list',
    isActive: true
  }
];
```

### PageLayoutComponent
A comprehensive layout component that combines header, action cards, and content.

## Usage Examples

### Basic Header Only
```html
<app-page-layout 
  [headerConfig]="headerConfig"
  [showActionCards]="false">
  
  <ng-template #content>
    <!-- Your content here -->
  </ng-template>
</app-page-layout>
```

### With Action Cards
```html
<app-page-layout 
  [headerConfig]="headerConfig"
  [actionCards]="actionCards"
  (actionCardClick)="onActionCardClick($event)">
  
  <ng-template #content>
    <!-- Your content here -->
  </ng-template>
</app-page-layout>
```

### Component Implementation
```typescript
export class YourComponent {
  headerConfig: PageHeaderConfig = {
    icon: 'pi pi-icon-name',
    title: 'Your Title',
    description: 'Your description'
  };

  actionCards: ActionCardConfig[] = [
    {
      icon: 'pi pi-action-icon',
      title: 'Action Title',
      description: 'Action description',
      action: 'action-name'
    }
  ];

  onActionCardClick(action: string): void {
    // Handle action card click
    console.log('Action clicked:', action);
  }
}
```

## Benefits

1. **Consistency**: All pages will have the same layout structure
2. **Maintainability**: Changes to the layout only need to be made in one place
3. **Reusability**: Easy to create new pages with the same layout
4. **Type Safety**: TypeScript interfaces ensure proper configuration
5. **Flexibility**: Can show/hide action cards and customize content area
