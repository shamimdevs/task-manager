# Task Manager
L

A full-featured Task Management System built with **Laravel 13** and **Tailwind CSS v4**, developed as a technical assessment for **Qtec Solution Limited**.

---
🌐 Live Demo

👉 https://task-manager.solvexbd.com/

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
- **User Leaderboard** — Ranked table of users by task completion with podium for top 3, stats summary, and pagination
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

### User Leaderboard
- Ranks all active users by completed tasks and overall completion rate
- **Top 3 podium** — 🥇🥈🥉 cards with colored avatar rings and glowing borders, ordered 2nd–1st–3rd for visual depth
- **Summary stats bar** — total ranked users, top performer's completions, average completion rate
- **Full rankings table** — rank medal, user avatar, completed / active / pending / total counts, animated gradient progress bar, color-coded rate
- **Paginated** — 10 users per page with item range indicator
- **Auto-generated daily at midnight** via Laravel scheduler (`leaderboard:generate` artisan command)
- **Manual generation** — admin can regenerate anytime via "Generate Now" button on the page
- Stored as snapshots in `leaderboard_snapshots` table; regeneration replaces the previous data

### UX & Design
- 100% responsive across all screen sizes (mobile, tablet, desktop)
- Dark glassmorphism UI with cyan/violet gradient theme
- Global loading overlay on all form submissions
- Active filter badges on search/filter forms
- Date picker icon styled to match dark theme (cyan tint)
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

### `leaderboard_snapshots`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users.id | |
| rank | tinyint | Position in rankings |
| completed_tasks | int | |
| in_progress_tasks | int | |
| pending_tasks | int | |
| cancelled_tasks | int | |
| total_tasks | int | |
| completion_rate | decimal(5,2) | Percentage |
| generated_at | timestamp | When this snapshot was created |
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
php artisan test tests/Feature/LeaderboardTest.php
php artisan test tests/Feature/TaskAssignedMailTest.php
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
PASS  Tests\Unit\TaskModelTest              15 tests
PASS  Tests\Feature\AdminTaskTest           19 tests
PASS  Tests\Feature\MyTaskTest              10 tests
PASS  Tests\Feature\LeaderboardTest         16 tests
PASS  Tests\Feature\TaskAssignedMailTest     9 tests
PASS  Tests\Feature\ExampleTest              1 test
PASS  Tests\Unit\ExampleTest                 1 test

Tests: 70 passed (137 assertions)  Duration: ~2.7s
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

**`tests/Feature/LeaderboardTest.php` — 16 tests**

| Group | Tests |
|---|---|
| Access Control | Guest → redirect login; regular user → 403; admin → 200; inactive admin → redirect login |
| Generate Access | Guest blocked; regular user blocked with 403 |
| Empty State | View renders correctly with no snapshot data |
| Generate | Creates snapshot in DB; correct task counts (completed, in-progress, pending, total, rate); ranks users by completed tasks; replaces previous snapshot on re-run; excludes inactive users |
| View Data | All view variables passed (entries, top3, generatedAt, totalUsers, avgRate) |
| Pagination | 10 per page; correct total; page 2 returns remaining items |

**`tests/Feature/TaskAssignedMailTest.php` — 9 tests**

| Group | Tests |
|---|---|
| Toggle ON | Email sent; sent to correct assignee; contains correct task data; exactly one mail per creation |
| Toggle OFF | Email not sent when toggle is `0`; email not sent when toggle is absent |
| Reassignment | Email sent when `assigned_to` changes; not sent when assignee unchanged; new assignee receives mail, old does not |

---

## Project Structure

```
task-manager/
├── app/
│   ├── Console/Commands/
│   │   └── GenerateLeaderboard.php         # Artisan command: ranks users, writes snapshot
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Register, Login, Logout
│   │   │   ├── DashboardController.php     # Role-based dashboard stats
│   │   │   ├── LeaderboardController.php   # Leaderboard view + manual generate
│   │   │   ├── MyTaskController.php        # User's own task view & status update
│   │   │   ├── PasswordResetController.php # Forgot/reset password flow
│   │   │   ├── ProfileController.php       # Profile view & update
│   │   │   ├── ReportController.php        # Admin & user reports
│   │   │   ├── TaskController.php          # Admin task CRUD + email on assign
│   │   │   └── UserController.php          # Admin user management
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php         # Blocks non-admins (403)
│   │       ├── AuthenticateMiddleware.php  # Blocks guests & inactive users
│   │       └── GuestMiddleware.php         # Redirects authenticated users
│   ├── Mail/
│   │   ├── PasswordResetMail.php           # Password reset email
│   │   └── TaskAssignedMail.php            # Task assignment notification email
│   └── Models/
│       ├── LeaderboardSnapshot.php         # Leaderboard snapshot model
│       ├── Task.php                        # Task model with helpers & relationships
│       └── User.php                        # User model with role/status helpers
├── database/
│   ├── factories/
│   │   ├── TaskFactory.php                 # States: pending, inProgress, completed, overdue, urgent
│   │   └── UserFactory.php                 # States: admin, inactive
│   └── migrations/
│       ├── ..._create_users_table.php
│       ├── ..._create_tasks_table.php
│       ├── ..._create_password_reset_tokens_table.php
│       └── ..._create_leaderboard_snapshots_table.php
├── resources/views/
│   ├── layouts/app.blade.php               # Main layout: nav, loading overlay, favicon
│   ├── welcome.blade.php                   # Landing page
│   ├── dashboard.blade.php                 # Role-aware dashboard
│   ├── report.blade.php                    # User report page
│   ├── auth/                               # Login, register, forgot/reset password
│   ├── emails/
│   │   ├── password-reset.blade.php        # Password reset HTML email
│   │   └── task-assigned.blade.php         # Task assignment HTML email
│   ├── profile/                            # Profile edit page
│   ├── tasks/                              # My Tasks page (user view)
│   └── admin/
│       ├── leaderboard.blade.php           # Leaderboard page with podium + table
│       ├── tasks/                          # Admin task CRUD views
│       ├── users/                          # Admin user CRUD views
│       └── report.blade.php               # Admin report page
├── routes/
│   ├── web.php                             # All application routes
│   └── console.php                         # Scheduler: leaderboard:generate daily at 00:00
├── tests/
│   ├── Unit/TaskModelTest.php              # 15 unit tests
│   └── Feature/
│       ├── AdminTaskTest.php               # 19 feature tests
│       ├── LeaderboardTest.php             # 16 feature tests
│       ├── MyTaskTest.php                  # 10 feature tests
│       └── TaskAssignedMailTest.php         # 9 feature tests
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
| Admin — Leaderboard | ❌ | ❌ | ✅ |

---

## Author

**Md Shamim Hossain**
GitHub: [@shamimhossain515419](https://github.com/shamimhossain515419)

---

*Developed for Qtec Solution Limited Technical Assessment — April 2026*
