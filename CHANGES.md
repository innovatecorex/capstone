# EncryptEd — Setup Pages & Sidebar Nav

This bundle:

- **Builds** the missing **Sections** CRUD page (`/admin/sections`).
- **Adds** an "Academic Setup" section to the sidebar with links for:
  - Academic Years (already existed, just unlinked)
  - Sections (new in this bundle)
  - Subjects (already existed, just unlinked)
  - Classrooms (already existed, just unlinked)

Apply, then go set things up in this order:

1. `/admin/academic-years` — create at least one Academic Year, set it Active
2. `/admin/subjects` — create your subjects, set Year Level on each
3. `/admin/sections` — create your sections (Grade 7 — St. Joseph, etc.)
4. `/admin/classrooms` — pick a year, add rooms
5. `/admin/users` — make sure faculty users exist (role 02)
6. `/admin/schedules/create` — now all dropdowns populate

## Files

**New (2)**
- `app/Http/Controllers/Admin/SectionController.php`
- `resources/views/admin/sections/index.blade.php`

**Modified (2)**
- `routes/web.php` — adds `/admin/sections` route group
- `resources/views/layouts/app.blade.php` — adds "Academic Setup" sidebar section

## Apply

```powershell
cd D:\xampp\htdocs\capstone
Expand-Archive -Path "$HOME\Downloads\setup-pages.zip" -DestinationPath . -Force
php artisan view:clear
php artisan route:clear
```

Hard refresh the browser (Ctrl+Shift+R). The sidebar will show a new
"Academic Setup" section with four links.

No new migrations — `sections` table already existed.
