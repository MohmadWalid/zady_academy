# Implementation Rules — Quran Academy Management System

> These rules are binding for all engineers. When a requirement is ambiguous, **do not assume — ask for clarification before writing code.**
>
> **Precedence:** Where this document conflicts with `PRD.md` or `System-Design.md` on a technical implementation detail, this document takes precedence.

---

## 1. General Principles

- Build for the MVP only. Do **not** add features not listed in the PRD.
- Keep all logic simple and readable. Avoid over-engineering.
- Mobile-first responsive UI. Every screen must be usable one-handed on a phone.
- Clean UI for non-technical users — no technical jargon exposed to the interface.
- All UI copy must be in Arabic. Follow the Arabic Language Audit table in `UI.md` exactly.
- All layouts must use `dir="rtl"` and `lang="ar"`.

---

## 2. Authentication Rules

### 2.1 No Passwords

- This system uses **no passwords**. Do not implement password hashing, password reset, or email/SMS verification.
- Do not install or use Laravel Breeze's password-based scaffolding. Build a custom `AccessCodeController` with a single login form.

### 2.2 Access Code Format & Generation

- Format: `ZADY-` + 4 uppercase alphanumeric characters derived from a UUID4 (e.g., `ZADY-F3A2`).
- Generation algorithm:
  1. Generate a UUID4.
  2. Strip hyphens, uppercase the string.
  3. Take the first 4 alphanumeric characters.
  4. Prepend `ZADY-`.
  5. Check `users.access_code` for uniqueness. If collision, repeat up to 5 times.
  6. If 5 collisions occur, log the failure and surface a descriptive error to the operator.
- Store access codes **in plain text** — never hash them. Admin must be able to read and relay a code to a user at any time.
- Enforce uniqueness at the DB level (`UNIQUE` index on `users.access_code`).

### 2.3 Login Flow Implementation

- Single form field: `access_code`.
- On submit: `SELECT * FROM users WHERE access_code = ? AND deleted_at IS NULL LIMIT 1`.
- On match: start a Laravel session, store `user_id`. Redirect to the role's dashboard route.
- On no match (including soft-deleted accounts): return a generic error — **"الكود غير صحيح، تواصل مع الإدارة"**. Do not distinguish between "code not found" and "account deleted".
- Rate-limit login attempts: max 10 attempts per IP per minute (use Laravel's `throttle` middleware).

### 2.4 Admin Seeding

- The 4 admin accounts are created via a database seeder (`AdminSeeder`).
- Each admin is seeded with a pre-configured `access_code` defined in the seeder file (or `.env`).
- Seeder must be idempotent: use `firstOrCreate` keyed on `phone` to avoid duplicates on re-seed.

### 2.5 One Code Per Family Rule

- When creating a new student, always check `users` for an existing record with `role = parent` and the supplied phone number **before** generating a new parent account.
- **Phone exists as parent** → reuse the existing parent. Skip code generation entirely.
- **Phone exists but role ≠ parent** → reject with error: "هذا الرقم مسجل بدور مختلف". Do not create a new user.
- **Phone does not exist** → create a new `users` row with `role = parent`, generate `access_code`, trigger the Code Reveal Screen.

### 2.6 Code Reveal Screen

- Must be shown **once**, immediately after a new parent, teacher, or secretary account is created.
- Displays the `access_code` in large, prominent text: **"كود الدخول: ZADY-XXXX"**.
- Include a "نسخ" (Copy) button to copy the code to clipboard.
- This screen must not be navigated to again after the initial reveal — the code is still visible on the user's profile for admin lookup, but there is no dedicated "show code" route.

### 2.7 Forgot Code (Admin Lookup)

- The admin-facing user/parent profile page must display `access_code` in plain text.
- No reset action is needed — reading and relaying the code is the entire flow.
- Code is visible even for soft-deleted accounts (shown in the Archive view).

---

## 3. Architecture & Code Rules

- Controllers must be thin. Move all business logic into dedicated Service classes (`app/Services/`).
- Use Laravel FormRequest classes for all input validation. No raw `$request->validate()` inside controllers.
- Use Laravel Policies for authorization checks. Never perform role checks inline in controllers.
- Use Eloquent observers or a shared `AuditableTrait` to automatically populate `created_by`, `updated_by`, `deleted_by`, and their corresponding timestamps on every write.
- Never bypass the Service layer for operations that change subscription or payment status.

---

## 4. Database & Schema Rules

### 4.1 Entities & Fields

- The `users` table covers **all** human actors. There is **no** separate `parents` table. Parents are identified by `users.role = 'parent'`.
- All entities listed below carry the full audit column set unless explicitly noted:
  - `created_by` (FK → users.id, nullable), `created_at`
  - `updated_by` (FK → users.id, nullable), `updated_at`
  - `deleted_by` (FK → users.id, nullable), `deleted_at`
- `group_sessions` is an exception: it has only `created_at` and `updated_at` (no soft delete).
- `attendance` is an exception: it has `created_by`, `created_at`, `updated_by`, `updated_at` (no soft delete, no `deleted_by` / `deleted_at`).

### 4.2 Unique Constraints (enforce at DB + application level)

| Table | Unique Constraint |
|---|---|
| `users` | `phone` |
| `users` | `access_code` |
| `payments` | `payment_code` |
| `enrollments` | `(student_id, group_id)` |
| `subscriptions` | `(student_id, group_id, month)` |
| `attendance` | `(student_id, group_id, date)` |

### 4.3 ENUMs

| Table.Column | Allowed Values |
|---|---|
| `users.role` | `admin`, `secretary`, `teacher`, `parent` |
| `groups.type` | `general`, `private`, `course` |
| `subscriptions.status` | `unpaid`, `pending`, `paid` |
| `payments.method` | `cash`, `transfer` |
| `payments.status` | `pending`, `approved`, `rejected`, `refunded` |

### 4.4 Soft Delete

- All entities with `deleted_at` use Laravel's `SoftDeletes` trait.
- All default Eloquent scopes **exclude** soft-deleted records automatically.
- Soft-deleted records must be shown in a dedicated "Archive / Recently Deleted" section on each Academic sub-page (Groups, Students, Teachers, Enrollments, Subscriptions).
- Hard delete is **not** permitted in the MVP — do not implement it.
- When soft-deleting, always set both `deleted_at = now()` and `deleted_by = auth()->id()`.

---

## 5. Audit Trail

Every create, update, and soft-delete operation must record:

| Event | Columns Populated |
|---|---|
| Create | `created_by = auth()->id()`, `created_at = now()` |
| Update | `updated_by = auth()->id()`, `updated_at = now()` |
| Soft Delete | `deleted_by = auth()->id()`, `deleted_at = now()` |

Implement this via a shared Eloquent trait or model observer applied to all auditable models. Never rely on controller code to populate these manually.

---

## 6. Role-Based Access Control

- Use a `RoleMiddleware` registered as `role` in the HTTP kernel.
- Protect all route groups by role. No route should be accessible without both `auth` and `role` middleware.
- Use Laravel Policies to gate individual model actions (create, update, delete, restore).
- If a user attempts to access a resource outside their role, return a 403 with an Arabic error page.

**Capability matrix:** See `System-Design.md §3 — Role Access Matrix` for the full per-feature breakdown. Enforce every row in that table at both the route and Policy level.

---

## 7. Subscription Rules

- Subscriptions are **auto-generated** by the `GenerateMonthlySubscriptions` artisan command, scheduled on the 1st of each month at 00:05.
- Use `firstOrCreate` (or `insertOrIgnore`) keyed on `(student_id, group_id, month)` to prevent duplicates.
- Only generate for enrollments where `active = true` and `deleted_at IS NULL`.
- `month` format: `YYYY-MM` (e.g., `2025-05`). Never store a day component.
- Default status on creation: `unpaid`.
- Subscriptions support soft delete (align with PRD §6.2). The soft-delete capability exists at the DB and model level, but **no "Delete Subscription" button is exposed in the MVP UI** — status changes are the primary operational action. A "Recently Deleted / Archive" sub-section must exist on the Subscriptions page for admin recovery of mistakenly soft-deleted records.

---

## 8. Payment Rules

### 8.1 Immutability

- Payments are **immutable** after creation. No edit endpoint for payment records.
- Corrections are handled by creating a new payment record (with a new `payment_code`).

### 8.2 Cash Payment Flow

1. Secretary/admin submits cash payment form (student → group/month → amount).
2. A confirmation dialog is shown before final submission.
3. On confirm:
   - Create `payments` row: `method = cash`, `status = approved`, `proof_image = null`.
   - Update linked `subscriptions.status → paid`.
4. Cash payments have no pending state — they are approved immediately.
5. Validate that the subscription is not already `paid` before recording. Return a descriptive error if so.

### 8.3 Transfer Payment Flow

1. Parent selects group + month, uploads proof image.
2. On submit:
   - Validate that the subscription is not already `paid` and no other `pending` payment exists for it.
   - Create `payments` row: `method = transfer`, `status = pending`, `proof_image = {path}`.
   - Update `subscriptions.status → pending`.
3. Show the confirmation screen: **"✅ تم استلام إيصالك وهو قيد المراجعة"** — do not redirect to the form.

### 8.4 Approval / Rejection Flow

1. Secretary/admin views pending payment with proof image.
2. Taps **تأكيد** (Approve) or **إلغاء** (Reject) — both require a confirmation dialog.
3. On approve: `payments.status → approved`, `subscriptions.status → paid`.
4. On reject: `payments.status → rejected`, `subscriptions.status → unpaid`.

### 8.5 Refund Flow (Admin Only)

1. Admin selects an approved payment and triggers a refund.
2. Confirmation dialog required.
3. On confirm: `payments.status → refunded`, `subscriptions.status → unpaid`.

### 8.6 Payment Code Generation

- Server-generated. Format: `PAY-{YYYYMM}-{zero-padded sequential}` (e.g., `PAY-202505-0042`).
- Must be unique — enforce at DB level (`UNIQUE` index on `payments.payment_code`).
- Never expose raw database IDs as payment references in the UI.

### 8.7 Proof Image Upload

- Accepted MIME types: `image/jpeg`, `image/png`.
- Maximum file size: 5 MB.
- Stored at `storage/app/private/proofs/{payment_code}_{timestamp}.{ext}` — **not** under the public disk.
- Never serve proof images via a direct public URL. Serve through an authenticated controller action that verifies the requesting user has the right to view the image (secretary, admin, or the parent who owns the payment).
- Reject uploads that fail MIME or size validation with a descriptive Arabic error.

---

## 9. Attendance Rules

- Record one row per `(student_id, group_id, date)`. Enforce with a `UNIQUE` constraint.
- `taken_by` must be set to `auth()->id()` at the point of record creation. It is **not** managed by `AuditableTrait` — set it explicitly in `AttendanceService`. It must reference a valid `users.id` with `role` in `[teacher, secretary, admin]`.
- Attendance dates are free-form — the system does not validate dates against `group_sessions`.
- **UI implementation: use full-row tap toggles, not checkboxes.** This is a hard requirement for mobile usability. Small `<input type="checkbox">` elements are prohibited on attendance screens.
- When displaying group cards in the Attendance view, always show: group name, teacher name, session time (from `group_sessions`), and student count (count of active enrollments).

---

## 10. Group Sessions Rules

- A group can have 1 or 2 `group_sessions` rows (weekly schedule). The max-2 constraint is enforced at the **application level** inside `GroupService` — reject any attempt to add a third session with an Arabic validation error. There is no DB-level constraint for this.
- `day` is stored as an English weekday string (e.g., `"Saturday"`, `"Monday"`). Display in Arabic in the UI.
- `time` is stored as `HH:MM` (MySQL `TIME` type).
- `group_sessions` does not support soft delete — schedule changes are hard updates/deletes.
- Session details (day + time) are shown on group cards throughout the app.

---

## 11. Confirmation Dialog Rules

A confirmation dialog is **mandatory** (not optional) before finalising any of the following actions:

- Recording a cash payment
- Approving a transfer payment
- Rejecting a transfer payment
- Issuing a refund
- Soft-deleting any record (student, group, teacher, enrollment, user)
- Any other action explicitly flagged with ⚠️ in `UI.md`

**Dialog must include:** a plain Arabic description of what will happen and the consequence. Use the approved Arabic terminology (e.g., "تأكيد" / "إلغاء", not "موافقة" / "رفض").

---

## 12. Empty State Rules

Every list or card view must render a **friendly Arabic empty-state message** when no records exist. A blank screen or a raw empty table is not acceptable.

Examples:
- "لا توجد مدفوعات معلقة حالياً"
- "لم يتم إضافة أي طلاب بعد"
- "لا توجد مجموعات مسجلة"
- "لا سجلات حضور لهذا اليوم"

---

## 13. Arabic / RTL Rules

- All Blade layouts must declare `<html dir="rtl" lang="ar">`.
- Tailwind CSS: use RTL-aware utilities (`rtl:` variants or dedicated RTL plugin).
- Navigation drawers, sidebars, form labels, and icon directions must all respect RTL flow.
- All user-facing strings must use plain Arabic as specified in the Language Audit table in `UI.md`. Do not expose English terms in the UI.
- Date display: use Gregorian dates but formatted for an Arabic-speaking audience (e.g., `٥ مايو ٢٠٢٥` or `2025/05/05`).

---

## 14. Error Handling

- Show descriptive, Arabic-language error messages for all user-facing failures.
- Never expose raw Laravel exceptions, stack traces, or English error messages to end users.
- Validation errors must appear inline (below the relevant field), not only in a toast.
- Server errors (5xx) must show a generic Arabic error page.

---

## 15. Security Rules

- All routes must be protected by `auth` middleware.
- Role middleware must be applied to every route group.
- Use Laravel's built-in CSRF protection on all forms — do not disable it.
- Proof image paths must not be guessable or publicly accessible. Store under `storage/app/private/proofs/` and serve exclusively via an authenticated controller action that verifies the requesting user's right to view the image. See §8.7.
- Never expose `created_by`, `updated_by`, or internal IDs in public-facing parent views.
