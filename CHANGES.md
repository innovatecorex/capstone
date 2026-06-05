# EncryptEd — Finished Grading Pages

Completes the grading-related pages across all three roles. Most of the
grading system was already built and working — this bundle fixes the two
genuine gaps found in an audit.

## What was already working (NOT touched)

- **Faculty gradebook** (`faculty-gradebook`, `faculty-gradebook-entry`) —
  full grade entry sheet with WW/PT/QA inputs, save draft, submit, finalize,
  drop/reinstate student, request unlock. 397-line entry sheet, complete.
- **Registrar grade lock** (`admin/grade-lock/index`) — lock per section,
  lock all, approve/deny unlock requests, per-section grade-status summary.
  Complete and wired.

## What this bundle fixes

### 1. Admin: Grading Quarters pages (were broken)
`grading-quarters/{index,create,edit}` extended the old layout with Tailwind
classes that don't render in this project (same bug as the academic-years
pages). Rewritten with the project's `.enc-card` design system:
- **index** — filterable table (by year + status), status pills, edit/delete
- **create** — proper form (academic year, period number, name, dates, status)
- **edit** — same, pre-filled

### 2. Registrar: Grades & Records (was a "Coming Soon" placeholder)
`registrar-grades` was a stub with no data. Now it's a real **Master Grade
Sheet** (FRS Registrar §f):
- Read-only view of every grade in the selected grading period
- Filter by grading period and by section+subject
- Shows section, subject, student, faculty, final grade, Passed/Failed remark,
  and grade status (draft/submitted/finalized/locked)
- Links to Grade Lock for the actual locking actions
- The controller method now queries real Grade data with eager loading

## Files (5)

**Modified (1)**
- `app/Http/Controllers/Dashboard/RegistrarUserDashboardController.php`
  (the `grades()` method now loads real data)

**Rewritten (4)**
- `resources/views/admin/registrars/grading-quarters/index.blade.php`
- `resources/views/admin/registrars/grading-quarters/create.blade.php`
- `resources/views/admin/registrars/grading-quarters/edit.blade.php`
- `resources/views/dashboard/registrar-grades.blade.php`

## Apply

```powershell
cd D:\xampp\htdocs\capstone
Expand-Archive -Path "$HOME\Downloads\grading-finish.zip" -DestinationPath . -Force
php artisan view:clear
```

No migration, no composer changes (existing files only).

## Smoke test

**Admin/Registrar — Grading Quarters:**
1. Navigate to Grading Quarters (in the academic-setup nav area, route
   `admin.grading-quarters.index`)
2. The page should render as a clean table, no giant icons
3. If you created an academic year earlier, its auto-created periods show here
4. Click "+ Add Grading Period" → form renders properly → create one
5. Edit it → change the dates → save

**Registrar — Grades & Records:**
1. Go to the registrar Grades page (route via `RegistrarUserDashboardController@grades`)
2. Pick a grading period and optionally a section+subject
3. If faculty have entered grades, they appear in the master sheet
4. If no grades yet, you get a friendly empty state (not a crash)

## Note on the registrar grades data

The master sheet only shows grades that exist in the `grades` table. On your
fresh MySQL database that's empty until faculty actually enter grades through
the faculty gradebook. To test with data: log in as faculty, open a class,
enter and save some grades, then view them as registrar.
