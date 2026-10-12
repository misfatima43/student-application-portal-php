# Student Application Portal — PHP & MySQL

A small scholarship application portal with server-side validation, prepared SQL statements, CSRF protection, and a simple success message.

## Requirements
- PHP 8.1+
- MySQL 8+
- Local server such as XAMPP, WAMP, or PHP's built-in server

## Setup
1. Create a database using `schema.sql`.
2. Set database credentials using environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, or update `config.php` for your local development environment.
3. Run the project through a PHP server. For local testing from this folder, use `php -S localhost:8000`.
4. Open `http://localhost:8000`.

## Security notes
This is a portfolio/demo implementation, not a production-ready admissions system. Before production, add authentication, rate limiting, email verification, secure deployment configuration, retention rules, and a proper privacy notice. Never collect national ID numbers or real sensitive documents in a public demo.
