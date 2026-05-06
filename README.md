<div align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20Logo%20Vertical/1%20Logo%20Vertical%20Black.svg" width="120" alt="Zady Academy Logo">
  <h1>? Zady Academy ?</h1>
  <p><strong>The ultimate Management System for Modern Quran Academies</strong></p>

  [![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)](https://php.net)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)
  [![TailwindCSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
</div>

---

## ?? Overview

**Zady Academy** is a high-performance, role-based management system tailored specifically for Quran academies. It bridges the gap between administrators, teachers, and parents, providing a seamless experience for managing student progress, attendance, and financial transactions.

Built with **Laravel 10**, it follows modern architectural patterns to ensure scalability, security, and ease of maintenance.

---

## ?? Key Features

### ?? Multi-Role Architecture
| Role | Responsibility |
| :--- | :--- |
| **Admin** | Full system control, financial auditing, and global reporting. |
| **Secretary** | Daily operations, student enrollments, and payment verification. |
| **Teacher** | Attendance tracking, student progress, and group management. |
| **Parent** | Monitoring child progress, payment history, and enrollment details. |

### ?? Advanced Modules
- **Dynamic Dashboards**: Real-time stats tailored to each user role.
- **Financial Suite**: Secure handling of subscriptions, one-time payments, and transfer proofs.
- **Attendance Hub**: Consolidated tracking logic with easy-to-use interfaces.
- **Data Integrity**: Full audit trails and soft-delete workflows for all critical entities.
- **Search & Filter**: Powerful search capabilities across students, payments, and groups.

---

## ??? Tech Stack & Architecture

- **Backend**: Laravel 10 (PHP 8.2+)
- **Frontend**: Blade Templates + TailwindCSS
- **State Management**: Service-Layer Delegation for complex business logic.
- **Security**: Policy-based authorization and encrypted storage for sensitive documents.
- **Environment**: Optimized for WSL2 / Ubuntu.

---

## ?? Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL 8.0

### Installation Steps

1. **Clone the Repo**
   `ash
   git clone https://github.com/MohmadWalid/zady_academy.git
   `

2. **Backend Setup**
   `ash
   composer install
   cp .env.example .env
   php artisan key:generate
   `

3. **Database & Migrations**
   `ash
   # Update your .env with database credentials
   php artisan migrate --seed
   `

4. **Frontend Setup**
   `ash
   npm install
   npm run dev
   `

5. **Launch**
   `ash
   php artisan serve
   `

---

## ?? Screen Previews

<div align="center">
  <img src="https://via.placeholder.com/800x400.png?text=Admin+Dashboard+Preview" alt="Dashboard Preview" width="600">
  <p><em>Example: Admin Analytics Dashboard (Modern & Responsive)</em></p>
</div>

---

## ?? Contributing

This project is currently for internal use. For major changes, please open an issue first to discuss what you would like to change.

---

<div align="center">
  Made with ?? for Quran Academies
</div>
