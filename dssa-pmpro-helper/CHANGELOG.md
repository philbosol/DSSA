# Changelog

All notable changes to the DSSA PMPro Helper plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2026-01-09

### Added
- Enhanced plugin header metadata including `Requires at least`, `Requires PHP`, `License URI`, `Tags`, and `Network` fields
- Comprehensive PHPDoc documentation for all functions and constants
- Languages directory with `.pot` template file for South African English translations
- Composer support with `composer.json` for modern PHP dependency management
- `.gitignore` file to exclude build artifacts and dependencies
- Detailed README.md with complete plugin documentation and developer guide
- CHANGELOG.md to track version history and changes
- Try-catch error handling in class initialization
- Logging for all class initialization steps
- File existence checks before requiring class files
- Dynamic method existence verification before calling `init()` methods

### Changed
- Improved dependency handling to show warning notices instead of errors, allowing partial functionality
- Enhanced admin notices for missing Paid Memberships Pro dependency with better messaging
- Refactored activation hook with comprehensive error handling and better error messages
- Improved deactivation hook with logging for scheduled hook cleanup
- Enhanced uninstall script with prepared statements for SQL security
- Better error handling in uninstall process with try-catch blocks
- Updated requirements check to allow plugin to continue with limited functionality
- Improved text domain loading for proper localization support

### Security
- Implemented prepared statements in uninstall.php using `$wpdb->prepare()` and `$wpdb->esc_like()`
- Added proper escaping for all user-facing output with `esc_html()`, `esc_attr()`, and `wp_kses_post()`
- Enhanced SQL security in database cleanup operations
- Added proper table name backtick escaping
- Implemented comprehensive input validation and sanitization

### Improved
- Code documentation with extensive inline comments explaining functionality
- Error messages are now translatable and follow WordPress i18n best practices
- Activation process now provides detailed error feedback to users
- Logging throughout initialization process for easier debugging
- Code organization and readability with clear section separators
- WordPress Coding Standards compliance throughout the codebase

### Developer Experience
- Added composer.json for package management and future PSR-4 autoloading
- Included PHPCS scripts in composer.json for code standards checking
- Created comprehensive README with architecture overview and development guidelines
- Better separation of concerns with improved code organization
- Enhanced error messages for easier troubleshooting

## [2.x.x] - Previous Versions
- Legacy features and functionality (documented in git history)

---

## Upgrade Notices

### 3.0.0
This version includes major improvements to plugin initialization, security, and documentation. The plugin now requires PHP 8.0+ and WordPress 6.0+. All existing functionality is preserved with enhanced error handling and security measures. No database migration is required.
