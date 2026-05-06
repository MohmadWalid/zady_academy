# PRD — Quran Academy Management System

## 1. Overview

A mobile-first, Arabic-RTL web application that replaces paper-based workflows for a Quran academy. It manages student enrollments, monthly subscriptions, cash and transfer payments, and session attendance across four distinct user roles.

**Stack:** Laravel + Blade, MySQL, Tailwind CSS, Arabic RTL interface.

---

## 2. Goals

- Eliminate payment disputes by creating a full, auditable paper trail for every payment.
- Reduce secretary error through guided workflows and mandatory confirmation dialogs.
- Give parents real-time visibility into their children's payment status.
- Give the admin a reliable daily revenue picture without manual aggregation.
- Replace all paper-based attendance sheets with a fast, mobile-optimised digital record.

---

## 3. Users & Roles

| Role          | Primary Responsibility                                                             |
| ------------- | ---------------------------------------------------------------------------------- |
| **Admin**     | Full system access. Revenue oversight, payment approval, academic management.      |
| **Secretary** | Daily operations: record cash payments, approve transfers, manage students/groups. |
| **Teacher**   | Take attendance for their assigned groups only.                                    |
| **Parent**    | View children's status, upload transfer proof, view payment history.               |

All roles share a single `users` table. Role is stored as an ENUM (`admin`, `secretary`, `teacher`, `parent`). There is no separate "Parent" table.

---

## 4. Core Features

### 4.0 Authentication

#### Login

- There are **no passwords** in this system. Every user authenticates with a single **access code**.
- Login screen: one text field ("أدخل الكود") + submit button ("دخول").
- The system matches the submitted code against `users.access_code`. On success, a session is started and the user is redirected to their role dashboard. On failure: "الكود غير صحيح، تواصل مع الإدارة".
- No reset flow, no email, no SMS — by design.

#### Account Creation by Role

| Role                | How Created                                                   | Code                         |
| ------------------- | ------------------------------------------------------------- | ---------------------------- |
| Admin (4)           | DB seeder, pre-set                                            | Fixed in seeder              |
| Secretary / Teacher | Admin creates via UI                                          | Auto-generated (`ZADY-XXXX`) |
| Parent              | Auto-created when a new student is added (if phone not in DB) | Auto-generated (`ZADY-XXXX`) |

#### Code Format

`ZADY-` followed by 4 uppercase alphanumeric characters derived from a UUID (e.g., `ZADY-F3A2`). Codes are globally unique and stored in plain text.

#### One Code Per Family

A parent account corresponds to a family, not an individual child. When adding a new student:

1. Secretary/admin enters the parent's phone number.
2. System checks: does a user with `role = parent` and that phone already exist?
   - **Yes** → link the new student to the existing parent. No new code generated.
   - **No** → create a new parent user, auto-generate `access_code`, and display the **Code Reveal Screen**.

#### Code Reveal Screen

Shown immediately after a new parent account is created. Displays the generated code prominently: **"كود الدخول: ZADY-F3A2"**. The admin copies it and sends it to the parent via WhatsApp or prints a slip. This screen is shown **once** at creation time — it is not a settings page.

#### Forgot Code Flow (Admin or Secretary Lookup)

- Parent calls or messages the academy.
- Admin or Secretary opens the student list, searches by student name, navigates to the parent's profile.
- The parent's `access_code` is visible in plain text on the profile page.
- Admin or Secretary reads it back to the parent. No system action required.

---

### 4.1 User Management

- Admin can create, edit, and soft-delete users of any role (teachers, secretaries, other admins).
- Secretary can create and manage parent accounts and student records.
- Login is via **access code** — there are no passwords. See §4.0 for full auth design.
- When admin or secretary creates a teacher or secretary account, the system auto-generates an `access_code` and displays the Code Reveal Screen.
- Soft-deleted users are recoverable from an Archive view. Their `access_code` remains visible to the admin for support purposes.

### 4.2 Student Management

- A student belongs to exactly one parent (a `users` record with `role = parent`).
- A parent can have multiple children (students).
- Student fields: name, age, phone (optional), parent.
- Secretary and Admin can add, edit, and soft-delete students.
- **Add Student flow:**
  1. Enter student details (name, age, phone).
  2. Enter parent's phone number.
  3. System checks if a parent with that phone already exists:
     - **Exists** → select and link to existing parent. No new account or code generated.
     - **New** → enter parent name; system creates the parent user, auto-generates `access_code`, and shows the Code Reveal Screen.
  4. Submit → student record created and linked to parent.
- Soft-deleted students appear in a "Recently Deleted / Archive" section and can be restored.

### 4.3 Group Management

- A group has a name, type (`general` / `private` / `course`), monthly price, and an assigned teacher.
- Each group has 1 or 2 weekly sessions defined in `group_sessions` (day + time).
- Secretary and Admin can add, edit, and soft-delete groups.
- Soft-deleted groups are recoverable from an Archive view.
- Group cards displayed in Attendance views must show: **group name, teacher name, session time, and student count**.

### 4.4 Enrollment

- A student can be enrolled in multiple groups simultaneously.
- Each enrollment is unique per `(student_id, group_id)` — duplicate enrollments are rejected.
- Enrollment has an `active` boolean flag; deactivating an enrollment stops subscription generation for that student/group.
- Secretary and Admin can add and soft-delete (deactivate) enrollments. Enrollment management is accessed from the **Student detail page** — an "إضافة لمجموعة" (Add to Group) action within the student's profile. There is no standalone Enrollments list page in the MVP.

### 4.5 Monthly Subscriptions

- Subscriptions are **auto-generated** on the 1st of each month for every active enrollment.
- Format: `YYYY-MM` (e.g., `2024-05`). Subscriptions are not tied to a specific payment date.
- Unique per `(student_id, group_id, month)` — no duplicates.
- Status flow:

```
unpaid  →  pending  (parent uploads proof)
pending →  paid     (approved by secretary/admin)
pending →  unpaid   (rejected by secretary/admin)
paid    →  unpaid   (admin issues refund — see §4.9)
```

- Secretary and Admin can manually update a subscription's month or group association (corrective action only).

### 4.6 Payment — Cash

**Actor:** Secretary or Admin.

**Flow:**

1. Search for student by name.
2. Select the relevant group and month (subscription).
3. Enter amount (pre-filled from `groups.monthly_price`, editable).
4. Submit → **confirmation dialog** appears: "هل تريد تسجيل دفعة نقدية بقيمة [X] لـ [Student] عن شهر [Month]؟"
5. On confirm: payment record created with `method = cash`, `status = approved`; subscription status → `paid`.

**Rules:**

- Cash payments are recorded by staff — parents cannot create cash payments.
- A unique `payment_code` is generated server-side (e.g., `PAY-YYYYMM-NNNN`).

### 4.7 Payment — Transfer (Parent Upload)

**Actor:** Parent.

**Flow:**

1. Parent opens their Payments page and taps "رفع إيصال" (Upload Proof).
2. Selects children, groups and month, attaches image (JPEG/PNG, max 5 MB)
   (As he can pay more than one subscription with single payment).
3. Submits → payment record created with `method = transfer`, `status = pending`; subscription → `pending`.
4. A dedicated confirmation screen is shown: **"✅ تم استلام إيصالك وهو قيد المراجعة"** — this prevents re-submission.

**Rules:**

- Only one active pending payment per subscription at a time.
- If a previous transfer was rejected, a new upload resets the cycle.

### 4.8 Payment — Transfer Approval

**Actor:** Secretary or Admin.

**Flow:**

1. Open Pending Approvals list.
2. Tap a pending payment card → view receipt image + students(if more than one - siblings)/groups/month details.
3. display the amount of money due for this payment based on payment details (students(if more than one - siblings)/groups/month)
4. Tap **تأكيد** (Approve) or **إلغاء** (Reject with reason note) → **confirmation dialog** required for both actions.
5. On approve: payment `status → approved`, subscription `status → paid`.
6. On reject: payment `status → rejected`, subscription `status → unpaid`, and send note for reason of rejection.

### 4.9 Payment (For cash & Transfer) — Refund

**Actor:** Admin.

- An approved payment can be marked `refunded` by an admin.
- Requires a confirmation dialog.
- Subscription status reverts to `unpaid` upon refund.
- Refund is a status change on the payment record, not a new record.

### 4.10 Payment History

- **Secretary / Admin:** Full payment history, searchable by student name, filterable by month and status.
- **Admin — Successful Transfers page:** Shows only `method = transfer, status = approved`. Mobile card layout: student name + amount (large), group + date (smaller). Tap opens detail view with receipt image, approved by, created_by, created_at.
- **Parent:** Filterable by month (default: current month). Shows all payments for their children.

### 4.11 Attendance

- Attendance is recorded per student × group × date.
- One record per combination; duplicate entries are rejected.
- **UI requirement:** Large-tap toggle buttons (full-row tap area) — **not** small checkboxes. This is mandatory for mobile usability.
- Attendance view shows daily group cards with: group name, teacher name, session time, student count. Cards are filterable by date and searchable by group name.
- Tapping a group card opens a student list with attendance toggles and session insights.
- Attendace is editable, teacher/secretary/admin can enter the attendance page again after the first attempt and edit attendance.

**Actors:**

- Teacher: takes or corrects attendance for their own assigned groups only.
- Secretary / Admin: can take or correct attendance for any group.

### 4.12 Dashboards

#### Admin Dashboard

- **Revenue Overview:** Total revenue for current month (sum of all `approved` payments), successful transfer count.
- **Insights:** Active student count, active group count for the current month.
- **Total Today's Cash:** Sum of all `method = cash, status = approved` payments with `created_at = today`. Identical value shown on Secretary dashboard.

#### Secretary Dashboard

- **Cash Payments widget:** Search bar for student name to quickly record cash payments.
- **Pending Approvals widget:** Count and list of pending transfer payments with approve/reject actions.
- **Total Today's Cash:** Same daily cash figure as Admin — no scoping difference.

#### Teacher Dashboard

- Cards for each assigned group. Each card: group name, session time, student count.
- Direct tap → opens attendance view for that group.

#### Parent Dashboard

- Alert summary: any payments in `pending` or `unpaid` status.
- Visual status cards per child: **مدفوع** / **غير مدفوع** / **قيد المراجعة** (Paid / Unpaid / Pending) — large, coloured text.

---

## 5. Role-Specific Navigation

Each role has a distinct navigation structure. Secretary and Admin share Academic and Attendance sections; Admin additionally has revenue insights and a Successful Transfers page. Teacher sees only their dashboard and Attendance. Parent sees their children's status and their own payment history and upload screen.

**Full per-role navigation structure (including Arabic labels) is defined in `UI.md` and is the authoritative spec.** Do not duplicate or diverge from it.

---

## 6. UX & Design Requirements

### 6.1 Confirmation Dialogs

A confirmation dialog with a plain-language Arabic description of the consequence is **mandatory** before any of the following actions:

- Submitting a cash payment
- Approving a transfer payment
- Rejecting a transfer payment
- Issuing a refund (admin)
- Soft-deleting any record (student, group, teacher, enrollment)
- Any status change that cannot be immediately reversed

### 6.2 Soft Delete & Archive

- All entities supporting soft delete (`users`, `students`, `groups`, `enrollments`, `subscriptions`, `payments`) must expose a "Recently Deleted / Archive" sub-section within their respective list page.
- Archive shows soft-deleted records with a **Restore** button.
- Hard delete is not available in the MVP.

### 6.3 Empty States

Every list view (students, groups, teachers, payment history, attendance, pending approvals) must render a friendly Arabic empty-state message instead of a blank screen.

Examples: "لا توجد مدفوعات معلقة حالياً" / "لم يتم تسجيل أي طلاب بعد".

### 6.4 Mobile-First

- All screens designed for mobile viewport first.
- No multi-column data tables on mobile — use card layouts.
- Attendance toggle targets must be full-row tap areas.
- Admin Successful Transfers page: card layout (student name + amount large, group + date smaller).

### 6.5 Arabic RTL

- All layouts use `dir="rtl"` and `lang="ar"`.
- Navigation drawers, form field alignments, icon directions, and pagination all respect RTL.
- UI copy must follow the Arabic Language Audit table (see `UI.md`):

| Use                    |
| ---------------------- |
| تسجيل اشتراك طالب      |
| إضافة طالب             |
| تحويل انستاباي / محفظة |
| تأكيد / إلغاء          |
| الإيراد اليومي         |
| عملية الدفع            |
| الأرشيف                |

---

## 7. Business Rules

1. A payment is only valid if it has a record in the `payments` table linked to a subscription. Unrecorded payments do not exist.
2. Subscriptions are monthly, not date-based. One subscription per student × group × month.
3. Subscriptions are auto-generated for all active enrollments on the 1st of each month.
4. A student cannot have two active enrollments in the same group simultaneously.
5. Cash payments are approved immediately upon recording (no pending state for cash).
6. Transfer payments start as `pending` until reviewed by secretary/admin.
7. A rejected transfer resets the subscription to `unpaid`, allowing re-submission.
8. Only Admin can issue refunds.
9. Soft-deleted records are excluded from all operational views but remain in the database.
10. Every write action records `created_by` / `updated_by` / `deleted_by` + corresponding timestamp.
11. Group sessions (schedule) are stored separately in `group_sessions`; one group can have 1–2 weekly sessions.

---

## 8. Edge Cases

| Scenario                                                              | Expected Behaviour                                                                                           |
| --------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------ |
| User enters an invalid or unknown access code                         | Show: "الكود غير صحيح، تواصل مع الإدارة". Do not indicate whether the code exists or not.                    |
| User enters a code belonging to a soft-deleted account                | Treat as invalid — same generic error. Do not allow login.                                                   |
| New student added with phone that already exists as a "student phone" | Block creation; show error: "هذا الرقم مسجل باسم طالب".                                                      |
| Access code collision during generation                               | Regenerate until a unique code is found. Max 5 attempts; if all fail, log and surface an error to the admin. |
| Admin tries to view a parent's code for a soft-deleted parent         | Code is still visible in the Archive view — admin may need it to support the parent.                         |
| Parent submits proof for an already-paid subscription                 | System rejects the upload with an error: "هذا الاشتراك مدفوع بالفعل".                                        |
| Secretary records cash for an already-paid subscription               | System rejects with an error: "الاشتراك مدفوع بالفعل".                                                       |
| Student deactivated mid-month                                         | Existing subscription for that month remains; no new subscription generated next month.                      |
| Attendance taken for a date with no session scheduled                 | Allowed — attendance date is free-form; no validation against `group_sessions`.                              |
| Same student enrolled in same group twice                             | Rejected at the database level (`UNIQUE` constraint on `student_id, group_id`).                              |
| Admin deletes a teacher assigned to active groups                     | Soft delete is blocked (or a warning shown) — teacher must be reassigned before deletion.                    |
| Payment proof image exceeds 5 MB or wrong format                      | Rejected at upload with a descriptive Arabic error message.                                                  |
| Subscription generated for a student already having one that month    | `INSERT IGNORE` / `firstOrCreate` logic prevents duplicates — no error surfaced to user.                     |

---

## 9. Constraints

- **Budget:** Low — no paid third-party services in MVP.
- **Scale:** ~2,000 students, ~120 groups.
- **Interface:** Arabic, RTL, mobile-first.
- **Connectivity:** Assumes reliable internet — no offline mode in MVP.

---

## 10. Out of Scope (MVP)

- Online payment gateways (Stripe, PayTabs, etc.)
- SMS or push notification automation
- Advanced analytics / reporting dashboards
- Student academic progress tracking (grades, memorization levels)
- Parent-teacher messaging
- Multi-branch / multi-academy support
