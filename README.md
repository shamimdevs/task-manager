# Task Manager

A full-featured Task Management System built with **Laravel 13** and **Tailwind CSS v4**, developed as a technical assessment for **Qtec Solution Limited**.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Architecture](#system-architecture)
- [Database Schema](#database-schema)
- [Installation & Setup](#installation--setup)
- [Running the Application](#running-the-application)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Role-Based Access Control](#role-based-access-control)

---

## Overview

Task Manager is a multi-role web application where **Admins** can create and assign tasks to users, and **Users** can view and update the status of tasks assigned to them. The system includes reporting, password reset via email, and a fully responsive UI that works across all screen sizes.

---

## Features

### Authentication
- Register, Login, Logout
- Forgot Password & Reset Password (email token-based)
- Custom middleware for auth, guest, and admin guards
- Inactive account blocking on login

### Admin Panel
- **User Management** — Create, edit, activate/deactivate users; view per-user task reports
- **Task Management** — Create, edit, delete tasks; assign to any user; set priority, status, due date
- **Task Assignment Email** — Automatically sends a styled HTML email to the assigned user on task creation; toggle on/off per task via a UI switch
- **Dashboard** — System-wide stats (total tasks, in-progress, completed, total users) + recent task feed
- **Reports** — Full task breakdown by status & priority, per-user stats table, overdue tracking

### User Panel
- **My Tasks** — View only tasks assigned to you; filter by status; search by title
- **Status Updates** — Mark tasks as In Progress or Completed
- **Dashboard** — Personal stats (my tasks, pending, in-progress, completed) + recent task feed
- **Profile** — Update name, email, designation, profile image, password
- **Reports** — Personal task breakdown with completion rate

### Email Notifications
- Styled HTML email sent to the assignee whenever a task is created
- Email includes task title, status badge, priority badge, due date, assigned-by name, and description
- Toggle switch on the create task form — admin can disable the notification per task
- Also triggers on task reassignment (when `assigned_to` changes during edit)
- Uses Laravel `Mailable` (`App\Mail\TaskAssignedMail`) with a dedicated Blade template (`emails/task-assigned`)
- Compatible with any SMTP provider (Gmail, Mailtrap, etc.)

### UX & Design
- 100% responsive across all screen sizes (mobile, tablet, desktop)
- Dark glassmorphism UI with cyan/violet gradient theme
- Global loading overlay on all form submissions
- Active filter badges on search/filter forms
- SVG favicon

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13.5.0 |
| Language | PHP 8.3 |
| Frontend | Tailwind CSS v4, Blade Templates |
| Build Tool | Vite 8 |
| Database | MySQL (production), SQLite in-memory (testing) |
| Testing | PHPUnit 12, Laravel Feature & Unit Tests |
| Dev Server | Laragon |

---

## System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                        Routes                           │
│  Public │ guest.custom middleware │ auth.custom + admin │
└────────────────────────┬────────────────────────────────┘
                         │
              ┌──────────▼──────────┐
              │     Controllers     │
              │  Auth  Dashboard    │
              │  Task  User         │
              │  MyTask  Report     │
              │  Profile Password   │
              └──────────┬──────────┘
                         │
              ┌──────────▼──────────┐
              │       Models        │
              │   User ◄──► Task    │
              └──────────┬──────────┘
                         │
              ┌──────────▼──────────┐
              │      Database       │
              │  users  tasks       │
              │  password_reset_    │
              │  tokens             │
              └─────────────────────┘
```

### Middleware Stack

| Middleware | Alias | Purpose |
|---|---|---|
| `AuthenticateMiddleware` | `auth.custom` | Redirects guests to login; blocks inactive accounts |
| `AdminMiddleware` | `admin` | Returns 403 for non-admin users |
| `GuestMiddleware` | `guest.custom` | Redirects authenticated users away from guest pages |

---

## Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | varchar | |
| email | varchar unique | |
| password | varchar | bcrypt hashed |
| role | enum | `admin`, `user` |
| status | enum | `active`, `inactive` |
| designation | varchar nullable | |
| profile_image | varchar nullable | |
| remember_token | varchar | |
| created_at / updated_at | timestamp | |

### `tasks`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| created_by | FK → users.id | Admin who created the task |
| assigned_to | FK → users.id | User the task is assigned to |
| title | varchar | |
| description | text nullable | |
| status | enum | `pending`, `in_progress`, `completed`, `cancelled` |
| priority | enum | `low`, `medium`, `high`, `urgent` |
| due_date | date nullable | |
| order | integer | For sorting |
| attachment | varchar nullable | |
| created_at / updated_at | timestamp | |

### `password_reset_tokens`
| Column | Type |
|---|---|
| email | varchar PK |
| token | varchar |
| created_at | timestamp |

---

## Installation & Setup

### Prerequisites
- PHP >= 8.3
- Composer
- Node.js >= 18 & npm
- MySQL (or any Laravel-supported database)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/shamimhossain515419/task-manager.git
cd task-manager

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=

# 7. Configure mail for password reset in .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@taskmanager.com
MAIL_FROM_NAME="Task Manager"

# 8. Run migrations
php artisan migrate

# 9. (Optional) Seed demo data
php artisan db:seed

# 10. Build frontend assets
npm run build
```

---

## Running the Application

```bash
# Start the Laravel development server
php artisan serve
# Visit: http://localhost:8000

# Or with Laragon — start Laragon and visit:
# http://task-manager.test
```

**Hot-reload during development:**
```bash
npm run dev
```

> **Note:** After modifying any Blade file with new Tailwind classes, run `npm run build` to recompile CSS.

---

## Testing

The test suite uses **SQLite in-memory** database — no extra database configuration needed.

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Files

```bash
php artisan test tests/Unit/TaskModelTest.php
php artisan test tests/Feature/AdminTaskTest.php
php artisan test tests/Feature/MyTaskTest.php
```

### Run a Single Test Method

```bash
php artisan test --filter test_admin_can_create_a_task
php artisan test --filter test_task_is_overdue_when_due_date_is_past_and_status_is_pending
```

### Other Useful Options

```bash
php artisan test --verbose          # Show all test names
php artisan test --stop-on-failure  # Stop at first failure
php artisan test tests/Unit         # Unit tests only
php artisan test tests/Feature      # Feature tests only
```

### Test Results

```
PASS  Tests\Unit\TaskModelTest        15 tests
PASS  Tests\Feature\AdminTaskTest     19 tests
PASS  Tests\Feature\MyTaskTest        10 tests
PASS  Tests\Feature\ExampleTest        1 test
PASS  Tests\Unit\ExampleTest           1 test

Tests: 46 passed (93 assertions)  Duration: ~3.7s
```

### Test Coverage Details

**`tests/Unit/TaskModelTest.php` — 15 tests**

| Test | What it verifies |
|---|---|
| `isOverdue()` | Past due + pending status = overdue |
| `isOverdue()` | Future due date = not overdue |
| `isOverdue()` | Completed task with past due date = not overdue |
| `isOverdue()` | Cancelled task with past due date = not overdue |
| `isOverdue()` | No due date = never overdue |
| `statusLabel()` | Correct labels for all 4 statuses |
| `statusLabel()` | Unknown status falls back to ucfirst |
| `statusColor()` | Correct Tailwind CSS classes per status |
| `priorityColor()` | Correct Tailwind CSS classes per priority |
| Relationship | Task `belongsTo` creator (User) |
| Relationship | Task `belongsTo` assignee (User) |
| Relationship | User `hasMany` assignedTasks |
| Relationship | User `hasMany` createdTasks |
| Casting | `due_date` casts to Carbon instance |
| Mass assignment | Task created with all fillable attributes |

**`tests/Feature/AdminTaskTest.php` — 19 tests**

| Group | Tests |
|---|---|
| Access Control | Guest → redirect login; regular user → 403; admin → 200; inactive admin → redirect login |
| Index / List | Admin sees all tasks; filter by status; filter by priority; search by title |
| Create | Form loads; successful creation + DB assertion; validates title, assigned_to, status, priority |
| Edit / Update | Form loads with task data; successful update; title required validation |
| Delete | Admin can delete; regular user blocked with 403 |

**`tests/Feature/MyTaskTest.php` — 10 tests**

| Group | Tests |
|---|---|
| Access Control | Guest → redirect login; authenticated user → 200 |
| Data Isolation | User sees only own assigned tasks, not other users' tasks |
| Search & Filter | Filter by status; search by title |
| Status Updates | Mark as in_progress; mark as completed; cannot update another user's task; invalid status rejected; guest blocked |

---

## Project Structure

```
task-manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Register, Login, Logout
│   │   │   ├── DashboardController.php     # Role-based dashboard stats
│   │   │   ├── MyTaskController.php        # User's own task view & status update
│   │   │   ├── PasswordResetController.php # Forgot/reset password flow
│   │   │   ├── ProfileController.php       # Profile view & update
│   │   │   ├── ReportController.php        # Admin & user reports
│   │   │   ├── TaskController.php          # Admin task CRUD
│   │   │   └── UserController.php          # Admin user management
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php         # Blocks non-admins (403)
│   │       ├── AuthenticateMiddleware.php  # Blocks guests & inactive users
│   │       └── GuestMiddleware.php         # Redirects authenticated users
│   └── Models/
│       ├── Task.php                        # Task model with helpers & relationships
│       └── User.php                        # User model with role/status helpers
├── database/
│   ├── factories/
│   │   ├── TaskFactory.php                 # States: pending, inProgress, completed, overdue, urgent
│   │   └── UserFactory.php                 # States: admin, inactive
│   └── migrations/
│       ├── ..._create_users_table.php
│       ├── ..._create_tasks_table.php
│       └── ..._create_password_reset_tokens_table.php
├── resources/views/
│   ├── layouts/app.blade.php               # Main layout: nav, loading overlay, favicon
│   ├── welcome.blade.php                   # Landing page
│   ├── dashboard.blade.php                 # Role-aware dashboard
│   ├── report.blade.php                    # User report page
│   ├── auth/                               # Login, register, forgot/reset password
│   ├── profile/                            # Profile edit page
│   ├── tasks/                              # My Tasks page (user view)
│   └── admin/
│       ├── tasks/                          # Admin task CRUD views
│       ├── users/                          # Admin user CRUD views
│       └── report.blade.php               # Admin report page
├── routes/web.php                          # All application routes
├── tests/
│   ├── Unit/TaskModelTest.php              # 15 unit tests
│   └── Feature/
│       ├── AdminTaskTest.php               # 19 feature tests
│       └── MyTaskTest.php                  # 10 feature tests
├── public/favicon.svg                      # SVG favicon
└── phpunit.xml                             # SQLite in-memory test config
```

---

## Role-Based Access Control

| Feature | Guest | User | Admin |
|---|:---:|:---:|:---:|
| View Welcome Page | ✅ | ✅ | ✅ |
| Register / Login | ✅ | ❌ | ❌ |
| Dashboard | ❌ | ✅ | ✅ |
| My Tasks (view & update status) | ❌ | ✅ | ❌ |
| Personal Report | ❌ | ✅ | ❌ |
| Profile Edit | ❌ | ✅ | ✅ |
| Admin — Task CRUD | ❌ | ❌ | ✅ |
| Admin — User Management | ❌ | ❌ | ✅ |
| Admin — System Report | ❌ | ❌ | ✅ |

---

## Author

**Md Shamim Hossain**
GitHub: [@shamimhossain515419](https://github.com/shamimhossain515419)

---

*Developed for Qtec Solution Limited Technical Assessment — April 2026*
