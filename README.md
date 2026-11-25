# Personal Lawyer Website

A single lawyer's public website and private administration portal, built with Laravel 12, Blade, Bootstrap 5, and MySQL.

## Requirements

- PHP 8.5 recommended (the project remains compatible with PHP 8.2+) with `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, and `zip`
- Composer
- MySQL

## Local setup

1. Install PHP dependencies with `composer install`.
2. Copy `.env.example` to `.env` if `.env` does not exist. Set `DB_CONNECTION=mysql`, `DB_DATABASE=Lawyer`, and the MySQL host, username, and password for your environment.
3. Create the `Lawyer` database in MySQL if it does not already exist. The default local XAMPP configuration uses `root` with an empty password; change those values when your MySQL setup differs.
4. Set `LAWYER_ADMIN_NAME`, `LAWYER_ADMIN_EMAIL`, and `LAWYER_ADMIN_PASSWORD` in `.env` to the individual lawyer's initial account credentials. The password is read only by the database seeder and stored using Laravel's password hashing.
5. Run `php artisan key:generate` and `php artisan migrate --seed`.
6. Run `php artisan storage:link` so uploaded profile portraits are available to the public website.
7. Start the local server with `php artisan serve` and open `/` for the public website or `/admin/login` for the private portal.

There is no public registration or user management screen. `LawyerAccountSeeder` creates the single lawyer account and refuses to add a second account. Do not commit `.env` or real credentials.

## Modules

- Public home, lawyer biography, legal services, practice areas, and contact form
- Private lawyer login, dashboard, profile and portrait management, service management, inquiry review, and landing-page content settings
- MySQL migrations for users, lawyer profiles, services, inquiries, and page content

Public profile, service, and landing-page copy is read from the database. Add and publish the content through the admin portal after signing in.

## Tests

Run the feature and unit tests with:

```sh
php artisan test
```

The test suite uses an in-memory SQLite database. The local MySQL schema was also migrated with the same Laravel migrations.
