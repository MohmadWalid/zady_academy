# System Design — Quran Academy Management System

## 1. Tech Stack

| Layer        | Choice                               | Notes                                              |
| ------------ | ------------------------------------ | -------------------------------------------------- |
| Backend      | Laravel (latest stable)              | MVC + Service layer                                |
| Frontend     | Laravel Blade + Alpine.js            | Server-rendered, RTL Arabic UI                     |
| Database     | MySQL 8+                             | InnoDB, strict mode                                |
| Auth         | Custom access-code session auth      | Single `access_code` field — no passwords. See §3. |
| File Storage | Local disk (public) or S3-compatible | Payment proof images                               |
| CSS          | Tailwind CSS                         | Mobile-first, RTL (`dir="rtl"`)                    |

---

## 2. Architecture

**Pattern:** Monolithic Laravel application, MVC with a Service layer for business logic.

```
app/
├── Http/
│   ├── Controllers/       # Thin controllers — delegate to services
│   ├── Middleware/        # RoleMiddleware, ConfirmationMiddleware
│   └── Requests/          # FormRequest validation per action
├── Services/
│   ├── SubscriptionService.php   # Auto-generation logic
│   ├── PaymentService.php        # Cash & transfer flows
│   └── AttendanceService.php     # Bulk record & insights
├── Models/                # Eloquent models with relationships
├── Policies/              # Role-based access per model
└── Console/Commands/
    └── GenerateMonthlySubscriptions.php  # Scheduled monthly
```

**Key principles:**

- Controllers must not contain business logic — delegate to Services.
- All destructive database operations (delete, reject, refund) go through Service methods that validate state before acting.
- Soft deletes are handled via `SoftDeletes` trait; queries must scope to non-deleted by default.

---

## 3. Authentication & Authorization

### Auth Model

All users authenticate with a single **access code** — there are no passwords in this system.

| Role                      | How account is created                   | Credential                      |
| ------------------------- | ---------------------------------------- | ------------------------------- |
| Admin (4 accounts)        | DB seeder (`php artisan db:seed`)        | Pre-set `access_code` in seeder |
| Secretary / Teacher (~30) | Created via Admin UI                     | Auto-generated `access_code`    |
| Parent (1000+)            | Auto-created when a new student is added | Auto-generated `access_code`    |

**Login flow:**

1. User opens the app and sees a single field: "أدخل كودك" (Enter your code).
2. They submit their `access_code` (e.g., `ZADY-F3A2`).
3. Laravel looks up the matching `users` row by `access_code`.
4. If found and not soft-deleted: session is started, user is redirected to their role dashboard.
5. If not found: show a generic Arabic error — "الكود غير صحيح، تواصل مع الإدارة".

**No password reset, no email, no SMS.** If a user forgets their code, an admin looks it up and reads it back manually (see PRD §4.0 — Forgot Code).

**Rate limiting:** Login attempts are throttled at the route level — max **5 attempts per IP per minute** using Laravel's built-in `throttle` middleware. On throttle: return HTTP 429 with the standard Arabic error message.

### Access Code Format

```
ZADY-{4 uppercase alphanumeric chars from UUID}
Examples: ZADY-F3A2 · ZADY-9B1C · ZADY-A7EF
```

- Generated server-side using the first 4 characters of a UUID4 (uppercase, alphanumeric only, hyphens stripped).
- Stored **in plain text** in `users.access_code` — admin must be able to read and relay it.
- Unique at the DB level (`UNIQUE` index on `users.access_code`).
- On collision (extremely rare): regenerate until unique.

### One Code Per Family

- An access code belongs to a **parent user account**, not to a student.
- A parent with multiple children has exactly one code, shared across all their children.
- When adding a new student: check `users.phone` first. If a parent with that phone already exists, reuse the account (no new code). If not, create a new parent user and generate a code.

### Role Middleware

```php
// routes/web.php
Route::middleware(['auth', 'role:admin'])->group(...);
Route::middleware(['auth', 'role:admin,secretary'])->group(...);
Route::middleware(['auth', 'role:teacher'])->group(...);
Route::middleware(['auth', 'role:parent'])->group(...);
```

### Role Access Matrix

| Feature                              | Admin | Secretary | Teacher | Parent |
| ------------------------------------ | :---: | :-------: | :-----: | :----: |
| Dashboard (own role view)            |  ✅   |    ✅     |   ✅    |   ✅   |
| Manage Users (Teachers, Secretaries) |  ✅   |    ❌     |   ❌    |   ❌   |
| Manage Students + Parents            |  ✅   |    ✅     |   ❌    |   ❌   |
| Manage Groups                        |  ✅   |    ✅     |   ❌    |   ❌   |
| Manage Enrollments                   |  ✅   |    ✅     |   ❌    |   ❌   |
| Manage Subscriptions                 |  ✅   |    ✅     |   ❌    |   ❌   |
| Record Cash Payment                  |  ✅   |    ✅     |   ❌    |   ❌   |
| Approve / Reject Transfer            |  ✅   |    ✅     |   ❌    |   ❌   |
| View Payment History                 |  ✅   |    ✅     |   ❌    |   ❌   |
| View Revenue Insights                |  ✅   |    ❌     |   ❌    |   ❌   |
| Take Attendance                      |  ✅   |    ✅     |   ✅    |   ❌   |
| Upload Payment Proof                 |  ❌   |    ❌     |   ❌    |   ✅   |
| View Own Children + Payments         |  ❌   |    ❌     |   ❌    |   ✅   |
| Soft Delete & Restore                |  ✅   |    ✅     |   ❌    |   ❌   |

---

## 4. Data Model

> **Authority:** The ERD (`ERD.html`) is the canonical schema. All models below match it exactly. The `users` table covers all human actors (admin, secretary, teacher, parent) — there is **no** separate `parents` table.

---

### 4.1 `users`

| Column        | Type                                           | Constraints                           |
| ------------- | ---------------------------------------------- | ------------------------------------- |
| `id`          | `int`                                          | PK, auto-increment                    |
| `name`        | `varchar(255)`                                 | not null                              |
| `phone`       | `varchar(20)`                                  | not null, unique                      |
| `access_code` | `varchar(10)`                                  | not null, unique — format `ZADY-XXXX` |
| `role`        | `enum('admin','secretary','teacher','parent')` | not null                              |
| `created_by`  | `int`                                          | FK → users.id, nullable               |
| `created_at`  | `timestamp`                                    | not null                              |
| `updated_by`  | `int`                                          | FK → users.id, nullable               |
| `updated_at`  | `timestamp`                                    | nullable                              |
| `deleted_at`  | `timestamp`                                    | nullable (soft delete)                |
| `deleted_by`  | `int`                                          | FK → users.id, nullable               |

**Notes:**

- Parents are users with `role = 'parent'`. Filtering by role is the only distinction.
- `access_code` is stored in plain text so admins can look it up and relay it to users.
- `created_by` is nullable to allow the first admin to be seeded.
- The ERD (`ERD.html`) predates the auth design in `login.md`; `access_code` is an addendum to the ERD schema.

---

### 4.2 `students`

| Column       | Type               | Constraints                             |
| ------------ | ------------------ | --------------------------------------- |
| `id`         | `int`              | PK, auto-increment                      |
| `name`       | `varchar(255)`     | not null                                |
| `parent_id`  | `int`              | FK → users.id (role = parent), not null |
| `age`        | `tinyint unsigned` | not null                                |
| `address`    | `text`             | nullable                                |
| `phone`      | `varchar(20)`      | nullable                                |
| `created_by` | `int`              | FK → users.id, nullable                 |
| `created_at` | `timestamp`        | not null                                |
| `updated_by` | `int`              | FK → users.id, nullable                 |
| `updated_at` | `timestamp`        | nullable                                |
| `deleted_at` | `timestamp`        | nullable (soft delete)                  |
| `deleted_by` | `int`              | FK → users.id, nullable                 |

---

### 4.3 `groups`

| Column          | Type                                 | Constraints                              |
| --------------- | ------------------------------------ | ---------------------------------------- |
| `id`            | `int`                                | PK, auto-increment                       |
| `name`          | `varchar(255)`                       | not null                                 |
| `type`          | `enum('general','private','course')` | not null                                 |
| `monthly_price` | `decimal(8,2)`                       | not null                                 |
| `teacher_id`    | `int`                                | FK → users.id (role = teacher), not null |
| `created_by`    | `int`                                | FK → users.id, nullable                  |
| `created_at`    | `timestamp`                          | not null                                 |
| `updated_by`    | `int`                                | FK → users.id, nullable                  |
| `updated_at`    | `timestamp`                          | nullable                                 |
| `deleted_at`    | `timestamp`                          | nullable (soft delete)                   |
| `deleted_by`    | `int`                                | FK → users.id, nullable                  |

---

### 4.4 `group_sessions`

Stores the recurring weekly schedule for each group. One group can have 1 or 2 sessions per week.

| Column       | Type          | Constraints                  |
| ------------ | ------------- | ---------------------------- |
| `id`         | `int`         | PK, auto-increment           |
| `group_id`   | `int`         | FK → groups.id, not null     |
| `day`        | `varchar(20)` | not null — e.g. `"Saturday"` |
| `time`       | `time`        | not null — `HH:MM` format    |
| `created_at` | `timestamp`   | not null                     |
| `updated_at` | `timestamp`   | nullable                     |

**Notes:**

- No soft delete on `group_sessions`; deleting a session is a hard delete (schedule change).
- The **max 2 sessions per group** constraint is enforced at the application layer in `GroupService`. There is no DB-level check — `GroupService` must count existing sessions before inserting a new one and reject with a validation error if the count would exceed 2.
- Displayed on group cards in the Attendance view (teacher name, day, time, student count).

---

### 4.5 `enrollments`

Pivot table linking students to groups. Acts as the source of truth for active memberships.

| Column       | Type        | Constraints                |
| ------------ | ----------- | -------------------------- |
| `id`         | `int`       | PK, auto-increment         |
| `student_id` | `int`       | FK → students.id, not null |
| `group_id`   | `int`       | FK → groups.id, not null   |
| `active`     | `boolean`   | not null, default true     |
| `created_by` | `int`       | FK → users.id, nullable    |
| `created_at` | `timestamp` | not null                   |
| `updated_by` | `int`       | FK → users.id, nullable    |
| `updated_at` | `timestamp` | nullable                   |
| `deleted_at` | `timestamp` | nullable (soft delete)     |
| `deleted_by` | `int`       | FK → users.id, nullable    |

**Unique constraint:** `(student_id, group_id)` — a student may only have one active enrollment per group at a time.

---

### 4.6 `subscriptions`

One record per student × group × month. Auto-generated by a scheduled command.

| Column       | Type                              | Constraints                  |
| ------------ | --------------------------------- | ---------------------------- |
| `id`         | `int`                             | PK, auto-increment           |
| `student_id` | `int`                             | FK → students.id, not null   |
| `group_id`   | `int`                             | FK → groups.id, not null     |
| `month`      | `varchar(7)`                      | not null — format `YYYY-MM`  |
| `status`     | `enum('unpaid','pending','paid')` | not null, default `'unpaid'` |
| `created_by` | `int`                             | FK → users.id, nullable      |
| `created_at` | `timestamp`                       | not null                     |
| `updated_by` | `int`                             | FK → users.id, nullable      |
| `updated_at` | `timestamp`                       | nullable                     |
| `deleted_at` | `timestamp`                       | nullable (soft delete)       |
| `deleted_by` | `int`                             | FK → users.id, nullable      |

**Unique constraint:** `(student_id, group_id, month)` — prevents duplicate subscriptions.

**Status transitions:**

```
unpaid  → pending  (parent uploads transfer proof)
pending → paid     (admin/secretary approves)
pending → unpaid   (admin/secretary rejects)
paid    → unpaid   (admin issues refund — payment.status → refunded)
```

---

### 4.7 `payments`

One payment record per transaction attempt. Multiple payments can reference the same subscription (e.g., after a rejection a new payment is submitted).

| Column            | Type                                               | Constraints                     |
| ----------------- | -------------------------------------------------- | ------------------------------- |
| `id`              | `int`                                              | PK, auto-increment              |
| `payment_code`    | `varchar(50)`                                      | not null, unique                |
| `subscription_id` | `int`                                              | FK → subscriptions.id, not null |
| `amount`          | `decimal(8,2)`                                     | not null                        |
| `method`          | `enum('cash','transfer')`                          | not null                        |
| `status`          | `enum('pending','approved','rejected','refunded')` | not null, default `'pending'`   |
| `proof_image`     | `varchar(255)`                                     | nullable — storage path         |
| `created_by`      | `int`                                              | FK → users.id, nullable         |
| `created_at`      | `timestamp`                                        | not null                        |
| `updated_by`      | `int`                                              | FK → users.id, nullable         |
| `updated_at`      | `timestamp`                                        | nullable                        |
| `deleted_at`      | `timestamp`                                        | nullable (soft delete)          |
| `deleted_by`      | `int`                                              | FK → users.id, nullable         |

**Status transitions:**

```
pending → approved  (admin/secretary confirms)
pending → rejected  (admin/secretary rejects)
approved → refunded (admin only)
```

**Notes:**

- `payment_code` is a server-generated unique reference (e.g., `PAY-20240501-0042`).
- Cash payments: `method = cash`, `proof_image = null`, `status` set immediately to `approved` on creation; subscription flips to `paid`.
- Transfer payments: `method = transfer`, `proof_image` required, `status = pending`; subscription flips to `pending`.
- Payments are **immutable after creation** — corrections require a new record.
- **Approver identity:** There is no separate `approved_by` column. The `updated_by` field records the identity of the last user to change a payment's status — effectively serving as `approved_by`, `rejected_by`, or `refunded_by`. The UI detail view for Successful Transfers displays `updated_by` as "تمت المراجعة بواسطة".

---

### 4.8 `attendance`

One record per student × group × date.

| Column       | Type        | Constraints                                |
| ------------ | ----------- | ------------------------------------------ |
| `id`         | `int`       | PK, auto-increment                         |
| `student_id` | `int`       | FK → students.id, not null                 |
| `group_id`   | `int`       | FK → groups.id, not null                   |
| `date`       | `date`      | not null                                   |
| `present`    | `boolean`   | not null, default false                    |
| `taken_by`   | `int`       | FK → users.id (teacher or admin), not null |
| `created_by` | `int`       | FK → users.id, nullable                    |
| `created_at` | `timestamp` | not null                                   |
| `updated_by` | `int`       | FK → users.id, nullable                    |
| `updated_at` | `timestamp` | nullable                                   |

**Unique constraint:** `(student_id, group_id, date)` — prevents duplicate attendance entries.

---

## 5. Relationships Summary

| From                  |  Cardinality  | To               | Via / Note                                |
| --------------------- | :-----------: | ---------------- | ----------------------------------------- |
| `users` (parent)      |     1 → N     | `students`       | `students.parent_id`                      |
| `users` (teacher)     |     1 → N     | `groups`         | `groups.teacher_id`                       |
| `groups`              |     1 → N     | `group_sessions` | `group_sessions.group_id` (max 2/week)    |
| `students` + `groups` |     N ↔ M     | each other       | via `enrollments`                         |
| `students` + `groups` | Composite → N | `subscriptions`  | unique on `(student_id, group_id, month)` |
| `subscriptions`       |     1 → N     | `payments`       | `payments.subscription_id`                |
| `students` + `groups` | Composite → N | `attendance`     | unique on `(student_id, group_id, date)`  |
| `users`               |     1 → N     | `attendance`     | `attendance.taken_by`                     |

---

## 6. Key Route Groups

```
GET  /                    → redirect to role dashboard

# Admin
GET  /admin/dashboard
GET  /admin/payments/...
GET  /admin/academic/...
GET  /admin/attendance/...

# Secretary
GET  /secretary/dashboard
GET  /secretary/payments/...
GET  /secretary/academic/...
GET  /secretary/attendance/...

# Teacher
GET  /teacher/dashboard
GET  /teacher/attendance/...

# Parent
GET  /parent/dashboard
GET  /parent/children/...
GET  /parent/payments/...
```

All routes protected by `auth` + `role:{role}` middleware.

---

## 7. File Storage

- Payment proof images stored under `storage/app/private/proofs/` — **not** the public disk. No `storage:link` is used for proof images.
- Images are served exclusively through an authenticated controller route (`GET /proofs/{payment_code}`) that verifies the requesting user is authorized to view the image (admin, secretary, or the parent who owns the payment) before streaming the file. Direct filesystem URLs are never exposed to clients.
- Accepted MIME types: `image/jpeg`, `image/png`. Max size: 5 MB.
- Filename: `{payment_code}_{timestamp}.{ext}` to prevent collision.

---

## 8. Scheduled Tasks

| Command                        | Schedule                 | Description                                                   |
| ------------------------------ | ------------------------ | ------------------------------------------------------------- |
| `GenerateMonthlySubscriptions` | 1st of each month, 00:05 | Creates `unpaid` subscription rows for all active enrollments |

---

## 9. Soft Delete Pattern

All entities with audit columns (`users`, `students`, `groups`, `enrollments`, `subscriptions`, `payments`) use Laravel's `SoftDeletes` trait.

- `deleted_at` is set to the current timestamp on deletion.
- `deleted_by` is set to the authenticated user's ID.
- All Eloquent queries automatically exclude soft-deleted records (`whereNull('deleted_at')`).
- Restore is available via an "Archive / Recently Deleted" sub-section on each Academic page.
- Hard delete is **not** permitted in the MVP.

---

## 10. Audit Trail

Every write operation must populate audit columns:

| Event       | Columns Set                                       |
| ----------- | ------------------------------------------------- |
| Create      | `created_by = auth()->id()`, `created_at = now()` |
| Update      | `updated_by = auth()->id()`, `updated_at = now()` |
| Soft Delete | `deleted_by = auth()->id()`, `deleted_at = now()` |

Use a base `AuditableTrait` or Eloquent observers to enforce this uniformly.

---

## 11. RTL / Localisation

- All Blade layouts use `dir="rtl"` and `lang="ar"`.
- Tailwind RTL plugin enabled (or manual `rtl:` variants).
- Arabic UI copy follows the terminology table in `UI.md` (Arabic Language Audit section).
- All date/time display uses Hijri calendar awareness where relevant, but storage is always Gregorian.
