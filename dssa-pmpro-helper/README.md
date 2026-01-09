# DSSA PMPro Helper Plugin

## Overview
Custom membership management system for the Dendrological Society of South Africa (DSSA). This plugin extends Paid Memberships Pro with specialized features for managing DSSA memberships, legacy member numbers, branch assignments, and South African payment integrations.

## Requirements
- WordPress 6.0 or higher
- PHP 8.0 or higher
- Paid Memberships Pro plugin (free version)

## Features
- Legacy membership number management
- CSV import/export for member data
- Branch management (28 South African branches)
- Custom checkout fields with bilingual labels (English/Afrikaans)
- Real-time membership number validation
- Paystack payment fee calculations
- Comprehensive audit logging
- User profile field management
- Custom admin interface

## Installation

### Option A: Manual Installation
1. Download the plugin folder
2. Upload to `/wp-content/plugins/dssa-pmpro-helper/`
3. Activate plugin in WordPress admin
4. Configure settings under Settings → DSSA PMPro Helper

### Option B: ZIP Installation
1. Create a ZIP of the plugin folder
2. Upload via WordPress Plugin → Add New → Upload Plugin
3. Activate the plugin

## Development

### Code Standards
This plugin follows WordPress Coding Standards and modern PHP best practices:
- PHP 8.0+ type declarations
- Comprehensive error handling
- Extensive inline documentation
- Localization support (South African English)
- Security-first approach with input validation and sanitization

### File Structure
```
dssa-pmpro-helper/
├── dssa-pmpro-helper.php    # Main plugin file
├── uninstall.php             # Uninstall handler
├── composer.json             # Composer configuration
├── includes/                 # Core classes
│   ├── class-database.php
│   ├── class-settings.php
│   ├── class-checkout-fields.php
│   ├── class-audit-log.php
│   ├── class-security.php
│   ├── class-legacy-members.php
│   ├── class-membership-levels.php
│   ├── class-registration.php
│   ├── class-branch-management.php
│   ├── class-login-system.php
│   └── class-admin-interface.php
├── assets/                   # CSS, JS, images
└── languages/                # Translation files
    └── dssa-pmpro-helper.pot
```

### Architecture
The plugin uses a modular architecture with separate classes for different concerns:

1. **Database Management** - Handles custom tables for legacy numbers, audit logs, and branches
2. **Settings** - Tab-based settings interface with WordPress Settings API
3. **Checkout Fields** - Custom PMPro checkout field integration
4. **Legacy Members** - CSV import/export and number management
5. **Security** - Permission checks and nonce verification
6. **Audit Log** - Tracks all member-related actions
7. **Registration** - Custom registration workflows
8. **Branch Management** - Geographic branch assignment
9. **Login System** - Custom authentication features
10. **Admin Interface** - Main admin menu and dashboard

### Class Initialization
Classes are loaded and initialized in the main plugin file using a controlled bootstrap process:
1. Constants defined
2. Helper functions registered
3. Requirements checked (PMPro dependency)
4. Text domain loaded for translations
5. Class files included with error handling
6. Classes initialized with dynamic method checking

### Composer Integration
A composer.json file is included for:
- Dependency management
- PSR-4 autoloading preparation (future enhancement)
- Development tools (PHPCS, etc.)

To install development dependencies:
```bash
composer install --dev
```

To check code standards:
```bash
composer phpcs
```

To automatically fix code style issues:
```bash
composer phpcbf
```

### Future Enhancements
- Full PSR-4 namespace implementation
- Automated testing suite (PHPUnit)
- Continuous integration (GitHub Actions)
- REST API endpoints
- Enhanced reporting and analytics

## Localization
The plugin is translation-ready with:
- Text domain: `dssa-pmpro-helper`
- Domain path: `/languages`
- POT template file included
- South African English as primary language

To create a translation:
1. Use the `.pot` file in `/languages` directory
2. Create a `.po` file for your locale (e.g., `dssa-pmpro-helper-af_ZA.po` for Afrikaans)
3. Compile to `.mo` file
4. Place in `/languages` directory

## Security
The plugin implements multiple security layers:
- Nonce verification for all forms
- Capability checks for admin actions
- Input sanitization and validation
- Output escaping
- SQL injection prevention via prepared statements
- XSS prevention

## Support
For issues and support:
- GitHub: https://github.com/philbosol/DSSA
- Website: https://dendro.co.za

## License
GPL v2 or later

## Credits
- **Author**: Phil Meyer / RMM New Generation Marketing
- **Website**: https://rmmm.co.za
- **Organization**: Dendrological Society of South Africa

## Changelog

### Version 3.0.0
- Enhanced plugin initialization with comprehensive error handling
- Improved dependency management with graceful degradation
- Added localization support with .pot template file
- Comprehensive inline documentation following PHPDoc standards
- Better admin notices for missing dependencies
- Enhanced activation/deactivation hooks with logging
- Security improvements throughout
- Added Composer support for modern development workflow
- Improved code organization and maintainability