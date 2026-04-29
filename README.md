# KSCAT KPI Performance Evaluation System

A web-based performance evaluation application for KS Cross Asia Technology (KSCAT). This project allows administrators to manage employee evaluations, department weight settings, and review performance summaries, while employees can securely view and approve their latest performance evaluation.

## Key Features

- Role-based access control: Admin and Employee views
- Admin dashboard with total employees, pending reviews, and completed evaluations
- Employee dashboard with latest weighted KPI score and review status
- Evaluation submission form with department-specific weighted scoring
- Department weight management for productivity, quality, attitude, teamwork, and role-specific KPI
- Employee evaluation approval with optional remarks and digital signature support
- Basic session protection and input sanitization

## Project Structure

- `index.php` — redirects users to login or role-specific dashboard
- `login.php` — authentication form and session initialization
- `dashboard.php` — shared dashboard for admin and employee roles
- `emp_dashboard.php` — employee landing page
- `evaluate.php` — admin evaluation submission form
- `manage_weights.php` — admin department weight configuration
- `my_evaluation.php` — employee evaluation review and approval page
- `process_evaluation.php` — evaluation processing endpoint
- `config.php` — database connection, error handling, and security configuration
- `functions.php` — reusable helpers for session protection and sanitization
- `style.css` / `script.js` — frontend styling and behavior

## Requirements

- PHP 7.4 or newer
- MySQL or MariaDB with `mysqli` extension enabled
- Web server such as Apache or Nginx
- Database with required tables: `users`, `evaluations`, `department_weights`

## Installation

1. Clone or copy the repository into your web server document root.
2. Configure the database connection in `config.php` or via environment variables:
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
   - `DB_CONNECT_TIMEOUT`
   - `APP_DEBUG` (set to `0` in production)
3. Create and populate the required tables, including admin and employee user accounts.
4. Open the app in your browser and log in using a valid user account.

## Usage

- Admin users can:
  - view dashboard metrics
  - submit performance evaluations for employees
  - manage department evaluation weights
- Employee users can:
  - view their latest performance summary
  - inspect score breakdowns and admin remarks
  - provide digital approval and remarks

## Security Notes

- `config.php` now supports restricted access and environment-driven configuration.
- `display_errors` is disabled when `APP_DEBUG` is not active.
- Use environment variables in production instead of hard-coding credentials.
- For stronger security, place `config.php` outside the public webroot if possible.
- Ensure the database and session storage are properly secured on your server.

## Customization

- Update `department_weights` to adjust how scores are weighted per department.
- Add or modify evaluation criteria inside `evaluate.php` and `my_evaluation.php` as needed.
- Customize the UI by editing `style.css` and the included AdminLTE markup.

## Notes

This application is designed as a lightweight KPI evaluation system and assumes the database schema is already provisioned. If you want to extend it, implement password hashing, role management enhancements, and stronger input validation.

