# User Interfaces

---

## Design System

> Mobile-first. Clean. Arabic (RTL). Built for non-technical users.

---

### Color Palette

#### Brand Colors

| Token                   | Hex       | Usage                              |
| ----------------------- | --------- | ---------------------------------- |
| `--color-primary`       | `#3B6EF8` | Primary actions, active nav, links |
| `--color-primary-light` | `#EEF2FF` | Button hover bg, selected row bg   |
| `--color-primary-dark`  | `#2A52C9` | Button pressed state               |

#### Neutral Colors

| Token                    | Hex       | Usage                                 |
| ------------------------ | --------- | ------------------------------------- |
| `--color-bg`             | `#F8F9FB` | App background                        |
| `--color-surface`        | `#FFFFFF` | Cards, modals, inputs                 |
| `--color-border`         | `#E4E7EC` | Card borders, dividers, input borders |
| `--color-text-primary`   | `#111827` | Headlines, labels, important values   |
| `--color-text-secondary` | `#6B7280` | Subtitles, helper text, timestamps    |
| `--color-text-disabled`  | `#B0B7C3` | Disabled inputs, placeholder text     |

#### State Colors

| Token                   | Hex       | Usage                                     |
| ----------------------- | --------- | ----------------------------------------- |
| `--color-success`       | `#16A34A` | Paid status, approved badge, confirmation |
| `--color-success-bg`    | `#F0FDF4` | Success badge background                  |
| `--color-warning`       | `#D97706` | Pending status, under review badge        |
| `--color-warning-bg`    | `#FFFBEB` | Warning badge background                  |
| `--color-danger`        | `#DC2626` | Unpaid/rejected status, delete action     |
| `--color-danger-bg`     | `#FEF2F2` | Danger badge background                   |
| `--color-danger-subtle` | `#FCA5A5` | Soft delete button, refund action         |

---

### Typography

> Font stack: **IBM Plex Sans Arabic** for Arabic body text (free, excellent RTL rendering). **Inter** as the Latin/numeral fallback.

```
font-family: 'IBM Plex Sans Arabic', 'Inter', sans-serif;
direction: rtl;
```

#### Type Scale

| Role         | Size | Weight | Line Height | Usage                                 |
| ------------ | ---- | ------ | ----------- | ------------------------------------- |
| `heading-xl` | 24px | 700    | 1.3         | Page titles, dashboard totals         |
| `heading-lg` | 20px | 700    | 1.3         | Section headers, card titles          |
| `heading-md` | 17px | 600    | 1.4         | Sub-section labels, modal titles      |
| `body-lg`    | 16px | 400    | 1.6         | Primary body, list items              |
| `body-md`    | 14px | 400    | 1.5         | Secondary body, descriptions          |
| `label`      | 13px | 500    | 1.4         | Form labels, nav items, table headers |
| `caption`    | 12px | 400    | 1.4         | Timestamps, helper text, badges       |

#### Rules

- **Status values** (Paid, Pending, Rejected) always use `heading-lg` + corresponding state color.
- **Monetary amounts** always use `heading-xl`, `--color-text-primary`, numerals rendered LTR within RTL flow.
- **Never go below 12px** on any visible text.

---

### Spacing & Layout

#### Base Unit

All spacing uses a **4px base grid**. Use multiples: `4, 8, 12, 16, 20, 24, 32, 40, 48`.

#### Page Layout

```
Max content width:  480px  (mobile-first, centered on larger screens)
Page horizontal padding:  16px
Page top padding:  16px
Section gap:  24px
```

#### Component Spacing

| Context                        | Value       |
| ------------------------------ | ----------- |
| Card internal padding          | `16px`      |
| Card gap between cards         | `12px`      |
| Form field gap (label → input) | `6px`       |
| Form field gap (field → field) | `16px`      |
| Button internal padding        | `12px 20px` |
| Nav item padding               | `14px 16px` |
| Modal internal padding         | `24px`      |
| Section title margin-bottom    | `12px`      |

#### Breakpoints

| Name             | Width       | Behavior                                   |
| ---------------- | ----------- | ------------------------------------------ |
| Mobile (default) | < 480px     | Single column, bottom nav                  |
| Tablet           | 480px–768px | Single column, side nav appears            |
| Desktop          | > 768px     | Side nav fixed, content max 480px centered |

---

### Components

---

#### Buttons

Three variants only — keep it simple.

**Primary Button** — main actions (Submit, Approve, Save)

```
Background:   --color-primary
Text:         #FFFFFF, label (13px, weight 600)
Border-radius: 10px
Padding:      12px 20px
Min height:   48px  ← mandatory for touch targets
Min width:    100px
Pressed:      --color-primary-dark
Disabled:     --color-border bg, --color-text-disabled text
```

**Danger Button** — destructive actions (Delete, Reject, Refund)

```
Background:   --color-danger-bg
Text:         --color-danger, label (13px, weight 600)
Border:       1px solid --color-danger-subtle
Border-radius: 10px
Padding:      12px 20px
Min height:   48px
```

**Ghost Button** — secondary/cancel actions

```
Background:   transparent
Text:         --color-text-secondary, label (13px, weight 500)
Border:       1px solid --color-border
Border-radius: 10px
Padding:      12px 20px
Min height:   48px
```

> **Rule:** Never place a Danger Button as the default/prominent action. Always pair it with a Ghost "Cancel" button.

---

#### Inputs & Form Fields

```
Height:         52px
Background:     --color-surface
Border:         1px solid --color-border
Border-radius:  10px
Padding:        0 14px
Font:           body-lg (16px), --color-text-primary
Placeholder:    --color-text-disabled

Focus state:
  Border: 1.5px solid --color-primary
  Box-shadow: 0 0 0 3px rgba(59,110,248,0.12)

Error state:
  Border: 1.5px solid --color-danger
  Helper text below: caption (12px), --color-danger

Label (above input):
  Font: label (13px, weight 500), --color-text-primary
  Margin-bottom: 6px
```

**Search Bar** — used in Cash Payments, Attendance, Transfers

```
Height:         48px
Border-radius:  24px  ← pill shape, visually distinct from regular inputs
Padding:        0 16px 0 44px  (icon on right in RTL)
Background:     --color-surface
Border:         1px solid --color-border
```

**Dropdown / Select**

```
Same dimensions as Input
Trailing icon: chevron-down, --color-text-secondary
Options list: --color-surface bg, 8px border-radius, shadow-md
Option height: 44px, body-md
Selected option: --color-primary-light bg, --color-primary text
```

---

#### Cards

**Standard Card** — groups, students, payment items

```
Background:     --color-surface
Border:         1px solid --color-border
Border-radius:  14px
Padding:        16px
Shadow:         0 1px 4px rgba(0,0,0,0.06)
```

**Stat Card** — dashboard totals (Today's Cash, Revenue)

```
Background:     --color-surface
Border:         1px solid --color-border
Border-radius:  14px
Padding:        20px 16px
Value:          heading-xl, --color-text-primary
Label:          caption, --color-text-secondary
Shadow:         0 1px 4px rgba(0,0,0,0.06)
```

**Group Card** — attendance & teacher dashboard

```
Same as Standard Card +
  Left border accent: 3px solid --color-primary  (visually anchors the card in RTL: right border)
  Group name: heading-md, --color-text-primary
  Teacher name: body-md, --color-text-secondary
  Time & student count: caption, --color-text-secondary
  Student count shown as:  👤 12 طالب
```

**Status Badge** — inline pill on payment/child cards

```
Border-radius:  20px
Padding:        4px 10px
Font:           caption (12px, weight 600)

Paid:     bg --color-success-bg,    text --color-success
Pending:  bg --color-warning-bg,    text --color-warning
Rejected: bg --color-danger-bg,     text --color-danger
Unpaid:   bg --color-danger-bg,     text --color-danger
```

---

#### Attendance Toggle Row

Replaces checkboxes. The entire row is tappable.

```
Row height:       64px  ← generous touch target
Padding:          0 16px
Border-bottom:    1px solid --color-border

Left side (RTL: right):
  Student name: body-lg, --color-text-primary
  Group/detail: caption, --color-text-secondary

Right side (RTL: left):
  Toggle pill: 52px × 30px
    Active (Present):   --color-success bg
    Inactive (Absent):  --color-border bg
    Thumb:              white circle, 2px shadow

Tapped row feedback: --color-primary-light bg flash (150ms)
```

---

#### Confirmation Dialog (Modal)

```
Overlay:        rgba(0,0,0,0.4)
Card:           --color-surface, border-radius 16px, padding 24px
Max width:      340px, horizontally centered
Title:          heading-md, --color-text-primary
Body:           body-md, --color-text-secondary (plain-language consequence description)
Button stack:   vertical, gap 10px
  Top:    Danger or Primary button (the confirming action)
  Bottom: Ghost button ("إلغاء" / Cancel)
```

---

#### Navigation

**Bottom Navigation Bar** — mobile default (< 480px)

```
Height:         60px + safe area inset
Background:     --color-surface
Border-top:     1px solid --color-border
Items:          4 max
  Icon:         24px, --color-text-secondary (inactive), --color-primary (active)
  Label:        caption (12px), same color logic
  Active item:  icon + label both --color-primary, small dot indicator below icon
```

**Side Drawer Nav** — tablet/desktop (≥ 480px)

```
Width:          240px
Background:     --color-surface
Border-left:    1px solid --color-border  (RTL — border on left side)
Section header: label (13px, weight 600), --color-text-disabled, uppercase, padding 16px 16px 6px
Nav item:       height 44px, padding 0 16px, body-md
  Inactive:     --color-text-primary
  Active:       --color-primary text, --color-primary-light bg, border-radius 8px
  Icon:         20px, trailing on right side in RTL
```

---

#### Empty State

```
Centered vertically and horizontally in the list area
Illustration:   simple single-color SVG icon (48px), --color-border colored
Heading:        heading-md, --color-text-secondary
Subtext:        body-md, --color-text-disabled
CTA button:     Primary button (only when an action makes sense, e.g. "إضافة طالب")
Top margin:     48px from top of list area
```

---

### Elevation & Shadows

```
shadow-sm:   0 1px 2px rgba(0,0,0,0.05)           → inputs on focus ring
shadow-md:   0 1px 4px rgba(0,0,0,0.06)           → cards, standard surfaces
shadow-lg:   0 4px 16px rgba(0,0,0,0.10)          → modals, bottom sheets, drawers
```

---

### Iconography

- Library: **Lucide Icons** (MIT, clean, consistent weight, RTL-neutral)
- Size: `20px` in nav and lists, `24px` in hero positions, `16px` inline with text
- Color: inherits from parent text color by default
- **Directional icons** (arrows, chevrons, back) must be mirrored for RTL — use CSS `transform: scaleX(-1)` or RTL-aware icon variants

---

### Motion & Feedback

Keep animations minimal — non-technical users find excessive motion confusing.

```
Default transition:   150ms ease-out  (taps, hovers, focus)
Modal open/close:     200ms ease-out  (slide up from bottom on mobile)
Confirmation flash:   150ms bg color flash on row tap
Page transitions:     none (instant) — avoids disorientation on mobile
```

---

## Login Screen (All Roles)

> **Single entry point — no role selection, no password.**

```
Layout:         Centered card, max 360px, vertically centered on screen
Logo / name:    Academy name or logo, heading-lg, top of card
```

- **One field:** label "كود الدخول", placeholder "ZADY-XXXX", `body-lg` input, autocapitalize off, autocomplete off.
- **Submit button:** "دخول" — Primary Button, full width.
- **Error state:** inline below the field, `caption`, `--color-danger` — "الكود غير صحيح، تواصل مع الإدارة". No hint about whether the code exists.
- **No** "Forgot Code" link, no "Sign up", no role selector.
- On success: server determines role, redirects to role dashboard — no intermediate screen.

### Code Reveal Screen

Shown **once** immediately after a new parent, teacher, or secretary account is created. This is a modal/bottom sheet, not a separate page.

```
Overlay:        rgba(0,0,0,0.4)
Card:           --color-surface, border-radius 20px, padding 28px
Max width:      360px
```

- **Icon:** key or lock icon, 40px, `--color-primary`, centered.
- **Title:** "كود الدخول الجديد", `heading-md`, centered.
- **Code block:** `ZADY-XXXX` displayed in `heading-xl`, `--color-primary`, monospaced, centered — visually prominent.
- **"نسخ الكود"** (Copy) Ghost Button below the code block — copies to clipboard, changes label to "✓ تم النسخ" for 2 seconds.
- **"تم — إغلاق"** Primary Button — dismisses the modal and returns to the previous list.
- Navigating away or tapping outside the modal also dismisses it. There is no way to re-open this screen.

---

## Secretary Role

### Home Dashboard

> **Focus:** Surface the two most frequent daily tasks immediately, alongside the daily revenue figure.

1. **Cash Payments:** Search bar for student names when receiving cash payments.
   - Enter payment details (group + month) and submit.
   - ⚠️ Confirmation dialog required before finalizing payment submission.
2. **Pending Approvals:** Card for managing incoming transfer proofs.
   - Approval page with receipt image and approve/reject actions.
   - ⚠️ Confirmation dialog required before approving or rejecting.
3. **Total Today's Cash:** Stat Card — read-only daily cash revenue figure (scope: all cash, same value as Admin view).

### Navigation

- **Home**
- **Payments**
  - Payment History
- **Academic**
  - Groups _(Add, edit, soft delete — with restore/archive view)_
  - Students _(Add with parent info + parent phone check → Code Reveal Screen if new parent, edit, soft delete — with restore/archive view)_
    - **Student detail page** includes an "إضافة لمجموعة" (Add to Group) action for managing enrollments. Enrollments are deactivated from the same student detail page. There is no standalone Enrollments list page.
  - Teachers _(Add → Code Reveal Screen on creation, edit, soft delete — with restore/archive view)_
  - Subscriptions _(Update month or group association — with restore/archive view for soft-deleted records)_
- **Attendance**
  - Daily Group Cards with search and date filters. Each card displays: group name, teacher name, time, and **student count**.
  - Group Details: Student list with personal details, Attendance Toggle Rows, and insights.

### UX Notes — Secretary

- **Destructive actions** (soft delete, refund status change) must always show a Confirmation Dialog with a plain description of the consequence.
- **Soft-deleted records** must be recoverable via a "Recently Deleted / Archive" section inside each Academic sub-page (Groups, Students, Teachers).
- **Attendance touch targets:** Use Attendance Toggle Rows — full-row tappable, 64px height minimum.
- **Empty states:** Every list view (students, groups, payment history, attendance) must show the Empty State component.

---

## Admin Role

### Home Dashboard

- **Revenue Overview:** Stat Card — total revenue and successful transfer payments.
- **Insights:** Stat Card — active students count and active groups per month.
- **Total Today's Cash:** Stat Card — same figure as Secretary, identical label.

### Successful Transfers Page

- Search Bar + month Dropdown filter.
- **Group Card layout per transfer** (not a table):
  - Primary: Student name (`heading-md`) + amount (`heading-lg`, `--color-primary`)
  - Secondary: Group + date (`caption`, `--color-text-secondary`)
  - Status Badge: Approved
  - Action: "عرض التفاصيل" → bottom sheet with receipt image, approved by, created_by, created_at.

### Navigation

- **Home**
- **Payments**
  - Pending Approvals _(receipt image, approve/reject with Confirmation Dialog)_
  - Cash Payments _(Search Bar, group + month entry, Confirmation Dialog on submit)_
  - Payment History / Successful Transfers
- **Academic**
  - Groups _(Add, edit, soft delete — with restore/archive view)_
  - Students _(Add with parent info + parent phone check → Code Reveal Screen if new parent, edit, soft delete — with restore/archive view)_
    - **Student detail page** includes an "إضافة لمجموعة" (Add to Group) action for managing enrollments. Enrollments are deactivated from the same student detail page. There is no standalone Enrollments list page.
  - Teachers _(Add → Code Reveal Screen on creation, edit, soft delete — with restore/archive view)_
  - Secretaries _(Add → Code Reveal Screen on creation, edit, soft delete — with restore/archive view)_
  - Subscriptions _(Update month or group association — with restore/archive view for soft-deleted records)_
- **Attendance**
  - Daily Group Cards with search and date filters. Each card displays: group name, teacher name, time, and **student count**.
  - Group Details: Student list with Attendance Toggle Rows and insights.

### Forgot Code (Admin & Secretary Lookup)

Accessible from the parent's profile page inside Academic → Students → tap student → displays `access_code` in plain text, styled as the Code Block (`heading-lg`, `--color-primary`, monospaced)..

- **"نسخ الكود"** Ghost Button — copies to clipboard.
- No reset or regenerate action. Reading and relaying is the entire flow.
- Code is also visible for soft-deleted parents inside the Archive view (admin may still need to support them).

### UX Notes — Admin

- All destructive actions (soft delete, refund, reject payment) require a Confirmation Dialog.
- Soft-deleted records must be recoverable via a "Recently Deleted / Archive" section per category.
- Empty State component required on all list views.

---

## Parent Role

### Home Dashboard

- Summary alert strip: `body-md`, `--color-warning`, `--color-warning-bg` background strip pinned below the top bar.
- Group Cards per child — Status Badge (Paid / Unpaid / Pending) shown prominently at top of card, `heading-lg` sized.

### My Children

- Group Card per child: child name (`heading-md`), enrolled group/s (`body-md`), status Badge.
- _UX: Use plain Arabic language — avoid technical terms. See Arabic Language Audit section below._

### Payments

- **Payment History:** Standard Cards in a list — default to current month. Month Dropdown at top.
- **Upload Proof:** 1–2 step flow — Step 1: pick image. Step 2: confirm and submit (Primary Button).
  - Post-submit: full-screen confirmation view — success icon (48px, `--color-success`), `heading-lg` message: "✅ تم استلام إيصالك وهو قيد المراجعة".

### Payment Details Page

> Reached by tapping a payment card in the Payment History list. Shows the full record for a **single payment transaction**.
>
> **Layout priority:** Status first, everything else secondary.

1. **Payment Status** — Status Badge at `heading-lg` scale, top of page.
2. Amount — `heading-xl`, `--color-text-primary`.
3. Group name and month — `body-md`, `--color-text-secondary`.
4. Collapsible "Details" section (`body-md`, `--color-text-secondary`): payment code, created_at timestamp, receipt image (if transfer), review status note.

> _Note: This is a detail view for one payment record. It is distinct from the Payment History list (which shows all payments for the parent's children, filterable by month)._

---

## Teacher Role

- **Dashboard:** Group Cards for assigned groups. Each card: group name (`heading-md`), time (`caption`), student count (`caption`, 👤 icon).
- **Attendance:** Same Group Card view as Secretary. Each card displays **student count**. Attendance Toggle Rows — large-tap, full-row.

---

## Arabic Language Audit

> All UI copy must use plain, everyday Arabic. The following technical terms must be replaced across all screens and roles:

| ❌ Avoid                                   | ✅ Use Instead         |
| ------------------------------------------ | ---------------------- |
| اشتراك (Subscription)                      | تسجيل اشتراك طالب      |
| تسجيل / إنرولمنت (Enrollment)              | إضافة طالب             |
| حوالة (Transfer — formal)                  | تحويل انستاباي / محفظة |
| (Receipt — formal)                         | الإيصال                |
| موافقة / رفض (Approve/Reject — admin tone) | تأكيد / إلغاء          |
| رصيد (Balance — financial)                 | الإيراد اليومي         |
| معاملة (Transaction)                       | عملية الدفع            |
| أرشفة (Archive)                            | الأرشيف                |
| (Payment Status — formal)                  | حالة الدفع             |
| (Student Data)                             | بيانات الطالب          |
| كلمة المرور / باسورد (Password)            | كود الدخول             |
| تسجيل دخول (Login — formal)                | دخول                   |
| نسيت كلمة المرور (Forgot password)         | تواصل مع الإدارة       |

---

## Project Configuration

- **Interface:** Arabic (RTL) — all layouts, navigation drawers, form alignments, and icon directions must respect RTL.
- **Confirmation dialogs:** Required for all destructive or irreversible actions across all roles.
- **Empty states:** Required on every list view across all roles.
- **Touch targets:** Minimum 48px height on all buttons; 64px height on all Attendance Toggle Rows.
- **Font loading:** Preload IBM Plex Sans Arabic (weights 400, 500, 600, 700) to avoid FOUT on first render.
