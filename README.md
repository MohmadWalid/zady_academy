# Zady Academy - Quran Academy Management System

A comprehensive management system designed for Quran academies to streamline administrative tasks, student tracking, and financial management.

## ?? Features

- **Role-Based Access Control**: Specialized dashboards and permissions for Admins, Secretaries, Teachers, and Parents.
- **Student Management**: Full CRUD operations for students, including enrollment tracking and profile management.
- **Attendance Tracking**: Consolidated attendance logic shared across roles with secure access.
- **Financial Module**: Manage payments, subscriptions, refunds, and proof-of-transfer verifications.
- **System Integrity**: Built-in audit trails and soft-delete/restore workflows to ensure data recovery and compliance.

## ??? Technical Stack

- **Framework**: [Laravel](https://laravel.com/)
- **Backend**: PHP
- **Database**: MySQL
- **Environment**: WSL (Ubuntu)

## ?? Installation

1. Clone the repository.
2. Install dependencies:
   `ash
   composer install
   npm install
   `
3. Configure environment variables:
   `ash
   cp .env.example .env
   php artisan key:generate
   `
4. Run migrations:
   `ash
   php artisan migrate
   `
5. Start the development server:
   `ash
   php artisan serve
   `

## ?? License

Internal use only.
