# Quran Academy — zady_app

## Environment
- Running inside WSL2 Ubuntu
- All commands use standard bash (never PowerShell, never wsl -e)
- Project path: /home/lido/zady_academy/zady_app
- Spec files: /home/lido/zady_academy/
- MySQL: host=127.0.0.1, port=3306, user=root, pass=root, db=zady_app
- PHP: 8.3, Composer installed

## Stack
Laravel + Blade + Alpine.js + MySQL 8 + Tailwind CSS RTL

## Non-negotiable Rules
- NO passwords — auth = access_code only (ZADY-XXXX, plain text in DB)
- NO Laravel Breeze or Sanctum
- Thin controllers — all logic in app/Services/
- FormRequest for ALL validation
- AuditableTrait on all auditable models
- All Blade layouts: <html dir="rtl" lang="ar">
- NO checkboxes on attendance screens — full-row tap toggles (64px)
- Hard delete PROHIBITED in MVP
- attendance has NO deleted_at / deleted_by
- group_sessions has NO soft delete
- taken_by on attendance set explicitly in AttendanceService only

## Spec Files (read before any feature work)
- /home/lido/zady_academy/PRD.md
- /home/lido/zady_academy/Implementation-Rules.md
- /home/lido/zady_academy/UI.md
- /home/lido/zady_academy/System-Design.md
