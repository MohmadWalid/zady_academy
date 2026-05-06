# Authentication — Canonical Reference

> ⚠️ This file previously contained informal planning notes. Those notes have been superseded by the formal specifications listed below. **Do not treat this file as authoritative.**

---

## Canonical Sources

All authentication design — login flow, access code format, code generation, code reveal screen, forgot-code flow, one-code-per-family rule, rate limiting, and admin seeding — is fully specified across three documents:

| Topic                                      | Location                                               |
| ------------------------------------------ | ------------------------------------------------------ |
| Product-level auth design (what & why)     | `PRD.md` — §4.0 Authentication                         |
| System-level implementation (how it works) | `System-Design.md` — §3 Authentication & Authorization |
| Engineering rules (how to build it)        | `Implementation-Rules.md` — §2 Authentication Rules    |

---

## Quick Reference

- **Login credential:** `access_code` only — no passwords, no email, no SMS.
- **Code format:** `ZADY-` + 4 uppercase alphanumeric chars from UUID4 (e.g., `ZADY-F3A2`).
- **One code per family:** If the parent's phone already exists, reuse the account — no new code.
- **Forgot code:** Admin looks up the parent's profile and reads the code back. No system action needed.
- **Rate limit:** 5 attempts per IP per minute (`throttle` middleware).
- **Admin accounts:** 4 accounts, seeded via `AdminSeeder` with pre-configured codes.
