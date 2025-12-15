# Presensi Admin Components

This directory contains the refactored components for the Presensi Admin page, making the code more modular, maintainable, and reusable.

## Component Structure

### Blade Components

1. **dashboard-header.blade.php**
   - Displays the main header with title, description, and current date
   - Props: `title`, `description`
   - Usage: `@component('Presensi.components.dashboard-header')`

2. **quick-guide.blade.php**
   - Displays a quick guide alert with workflow instructions
   - Props: `fullText`, `shortText`
   - Usage: `@component('Presensi.components.quick-guide')`

3. **dashboard-summary-cards.blade.php**
   - Displays summary cards for key metrics (stores, settings, holidays, approvals)
   - Props: `totalStores`, `activeSettings`, `totalHolidays`, `pendingApprovals`
   - Usage: `@component('Presensi.components.dashboard-summary-cards')`

4. **store-management.blade.php**
   - Displays the store management section with map and form
   - Props: `stepNumber`
   - Usage: `@component('Presensi.components.store-management')`

5. **attendance-settings.blade.php**
   - Displays the attendance settings configuration form
   - Props: `stepNumber`
   - Usage: `@component('Presensi.components.attendance-settings')`

6. **holiday-management.blade.php**
   - Displays the holiday management form and list
   - Props: `stepNumber`
   - Usage: `@component('Presensi.components.holiday-management')`

7. **approval-workflow.blade.php**
   - Displays the approval workflow table for pending attendance approvals
   - Props: `stepNumber`, `pendingCount`
   - Usage: `@component('Presensi.components.approval-workflow')`

### JavaScript Components

1. **js/store-map.js**
   - Handles map functionality for store management
   - Functions: `initMap()`, `setPoint()`, `setRadius()`, `getCurrentLocation()`, `searchLocation()`

2. **js/store-management.js**
   - Handles store CRUD operations and form management
   - Functions: `loadStores()`, `fillStore()`, `resetStoreForm()`, `saveStore()`

3. **js/attendance-settings.js**
   - Handles attendance settings configuration
   - Functions: `loadSetting()`, `saveAttendanceSettings()`

4. **js/holiday-management.js**
   - Handles holiday CRUD operations
   - Functions: `loadHolidays()`, `addHoliday()`, `delHoliday()`, `confirmDelHoliday()`

5. **js/approval-workflow.js**
   - Handles attendance approval operations
   - Functions: `loadApprovals()`, `reviewApproval()`, `confirmReviewApproval()`

6. **js/admin-main.js**
   - Initializes all components and handles global functionality
   - Functions: `initializeComponents()`

## Benefits of Component-Based Architecture

1. **Modularity**: Each component is self-contained with its own logic and presentation
2. **Reusability**: Components can be reused across different pages or projects
3. **Maintainability**: Easier to debug and update individual components
4. **Separation of Concerns**: UI, logic, and data are properly separated
5. **Readability**: Cleaner, more organized code structure
6. **Testing**: Individual components can be tested in isolation

## Usage

To use these components in your Blade templates:

```blade
@component('Presensi.components.component-name', ['prop' => 'value'])
    <!-- Optional slot content -->
@endcomponent
```

## Dependencies

- Bootstrap 5 for styling
- Font Awesome for icons
- Leaflet for mapping functionality
- SweetAlert2 for notifications
- Laravel Blade for templating

## File Organization

```
resources/views/Presensi/components/
├── README.md
├── dashboard-header.blade.php
├── quick-guide.blade.php
├── dashboard-summary-cards.blade.php
├── store-management.blade.php
├── attendance-settings.blade.php
├── holiday-management.blade.php
├── approval-workflow.blade.php
└── js/
    ├── store-map.js
    ├── store-management.js
    ├── attendance-settings.js
    ├── holiday-management.js
    ├── approval-workflow.js
    └── admin-main.js