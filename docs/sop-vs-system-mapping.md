# SOP vs. System Mapping

Maps each real-world Academy SOP step to the system screen that implements it.
Columns: **SOP Step** | **System Route / Screen** | **Status After** | **DB Tables Written**

> **Legend**
> - `CONFIRM WITH SCHOOL` — step exists in system but school must confirm it matches their actual procedure
> - `⚠️ NO IMPLEMENTATION` — SOP step has no corresponding system screen
> - `⚠️ NO SOP BASIS` — system feature exists but is not grounded in a confirmed SOP step

---

## Phase 0 — Academic Calendar & Curriculum Setup
*(Admin prerequisite — done once per school year before admissions open)*

| SOP Step | System Route / Screen | Status After | DB Tables Written |
|---|---|---|---|
| Admin creates the new academic year (school year dates, label) | `GET /admin/academic-years/create` → `POST /admin/academic-years` | academic_year.status = `upcoming` | `academic_years` |
| Admin sets active academic year | `PATCH /admin/academic-years/{id}/toggle` | academic_year.status = `active` | `academic_years` |
| Admin defines grading quarters (Q1–Q4, dates) | `POST /admin/grading-quarters` | quarter.status = `upcoming` | `grading_quarters` |
| Admin activates the current grading quarter | `PATCH /admin/grading-quarters/{quarter}/activate` | quarter.status = `active` | `grading_quarters` |
| Admin adds subjects to the master registry | `POST /admin/subjects` | subject.status = `active` | `subjects` |
| Admin maps subjects to grade level / academic year (curriculum) | `POST /admin/curriculum-mappings` | — | `curriculum_mappings` |
| Admin registers classrooms | `POST /admin/classrooms` | classroom.status = `active` | `classrooms` |
| Admin creates sections (Grade 7-A, etc.) for the year | `POST /admin/sections` | section.status = `active` | `sections` |
| Admin builds the master schedule (section + subject + room + day/time) | `POST /admin/schedules` | schedule.status = `tba` | `schedules` |
| Admin assigns faculty to each schedule slot | `POST /admin/schedules/{schedule}/assign-faculty` | schedule.status = `assigned` | `schedules`, `section_subjects` |
| `CONFIRM WITH SCHOOL` — Who sets enrollment fees for the year? | `GET /admin/payments/fees` → `POST /admin/payments/fees` | — | `payment_fees` *(table name unverified)* |

---

## Phase 1 — Admissions
*(Applicant applies; school reviews, tests, and decides)*

| SOP Step | System Route / Screen | Status After | DB Tables Written |
|---|---|---|---|
| Prospective student fills out online application form (public, no login) | `GET /apply` → `POST /apply` | applicant.status = `pending` | `applicants`, `applicant_documents` |
| Applicant receives reference number confirmation page | `GET /apply/thanks/{reference}` | — | — |
| `CONFIRM WITH SCHOOL` — Does admissions staff call/contact the applicant before review? | ⚠️ NO IMPLEMENTATION (phone/email handled outside system) | — | — |
| Staff opens application for review | `GET /admin/applicants/{applicant}` or `GET /registrar/applicants/{applicant}` | — | — |
| Staff sets status to "Under Review" | `PATCH /admin/applicants/{applicant}/status` or `PATCH /registrar/applicants/{applicant}/status` | applicant.status = `under_review` | `applicants` |
| `CONFIRM WITH SCHOOL` — Is there a formal entrance exam? Who administers it? | `GET /admin/guidance-testing/{applicant}/record` → `POST /admin/guidance-testing/{applicant}/record` | applicant.status = `for_test` (via status update) | `guidance_test_results` *(table name unverified)* |
| `CONFIRM WITH SCHOOL` — Is the entrance test the same as "Guidance Testing" in the system, or are they separate steps? | Legacy route also exists: `POST /admin/entrance-tests/{applicant}/record` | applicant.status = `tested` (via status update) | `entrance_test_results` *(table name unverified)* |
| `CONFIRM WITH SCHOOL` — Is there a guidance interview step? | ⚠️ NO IMPLEMENTATION (no dedicated guidance interview screen) | — | — |
| Staff accepts, rejects, or waitlists the applicant | `PATCH /admin/applicants/{applicant}/status` (admin) or `PATCH /registrar/applicants/{applicant}/status` (registrar) | applicant.status = `accepted` / `rejected` / `waitlisted` | `applicants` |
| System sends acceptance email notification | Triggered automatically on status → `accepted` (AcceptanceNoticeMail / WaitlistNoticeMail) | — | — |

---

## Phase 2 — Enrollment
*(Accepted applicant submits documents; staff verifies and creates student account)*

| SOP Step | System Route / Screen | Status After | DB Tables Written |
|---|---|---|---|
| `CONFIRM WITH SCHOOL` — Does the applicant submit physical documents to the registrar's office? | ⚠️ NO IMPLEMENTATION (physical receipt handled offline; file uploads captured at application time via `applicant_documents`) | — | — |
| Registrar checks submitted documents against requirements | `GET /registrar/applicants/{applicant}` → document checklist | — | — |
| Registrar marks requirements as confirmed | `POST /registrar/applicants/{applicant}/requirements` | — | `applicant_requirement_checks` |
| Registrar sets applicant as "Eligible for Enrollment" (all docs confirmed) | `PATCH /registrar/applicants/{applicant}/status` | applicant.status = `eligible_for_enrollment` | `applicants` |
| `CONFIRM WITH SCHOOL` — Does payment happen before or after account creation? | System supports pay-first: student pays after account is created via student portal | — | — |
| Registrar/Admin creates student system account (triggers section auto-assignment) | `POST /admin/applicants/{applicant}/create-account` or `POST /registrar/applicants/{applicant}/create-account` | applicant.status = `enrolled`; enrollment.status = `enrolled` | `users`, `enrollments` |
| Student logs in for the first time and resets forced password | `GET /password/reset-required` → `POST /password/reset-required` | user.must_reset_password = false | `users` |
| Student submits enrollment payment (proof of payment upload) | `GET /student/payments` → `POST /student/payments` | payment.status = `pending` | `payments` |
| Admin confirms payment receipt | `POST /admin/payments/{payment}/confirm` | payment.status = `confirmed` | `payments` |
| `CONFIRM WITH SCHOOL` — Is payment confirmation what unblocks the student from seeing grades? | ⚠️ Payment status affects UI display; confirm whether unpaid students are blocked from the portal entirely | — | — |
| Registrar performs advising (reviews / adjusts student's subject list for the year) | `GET /registrar/advising/{student}` → `POST /registrar/advising/{student}/add-subject` / `remove-subject` | — | `section_subjects`, advising tables |
| Registrar confirms advising plan | `POST /registrar/advising/{student}/confirm` | — | advising records |
| Registrar finalizes enrollment (locks section assignment; creates grade shells for all subjects × quarters) | `GET /registrar/enrollment/finalize/{student}` → `POST /registrar/enrollment/finalize/{student}` | enrollment.finalized_at = now(); grade.status = `draft` (one row per subject per quarter) | `enrollments`, `grades` |

---

## Phase 3 — Academic Execution
*(Classes are running; faculty records attendance and enters grades)*

| SOP Step | System Route / Screen | Status After | DB Tables Written |
|---|---|---|---|
| Faculty views their class list and schedule | `GET /faculty/my-classes` / `GET /faculty/my-schedule` | — | — |
| Faculty takes attendance for a class session | `GET /faculty/attendance` → `POST /faculty/attendance` | — | `attendances` |
| Student views own attendance record | `GET /student/attendance` | — | — (read only) |
| `CONFIRM WITH SCHOOL` — At what point does a student's attendance trigger an academic warning? | ⚠️ NO IMPLEMENTATION (no automated attendance-threshold alert in system) | — | — |
| Faculty creates and publishes assignment | `POST /faculty/assignments` → `PATCH /faculty/assignments/{assignment}/publish` | assignment.status = `published` | `assignments` |
| Student submits assignment | `POST /student/assignments/{assignment}/submit` | submission.status = `submitted` | `assignment_submissions` |
| Faculty grades assignment submission | `PATCH /faculty/assignments/submissions/{submission}/grade` | — | `assignment_submissions` |
| Faculty enters quarterly grades (Written Works, Performance Tasks, Quarterly Assessment) | `GET /faculty/classes/{sectionSubject}` → `POST /faculty/classes/{sectionSubject}/save-draft` | grade.status = `draft` | `grades` |
| Faculty submits final quarterly grades | `POST /faculty/classes/{sectionSubject}/submit` | grade.status = `submitted` | `grades` |

---

## Phase 4 — Grade Reporting
*(Registrar verifies, finalizes, and locks grades; students receive report cards)*

| SOP Step | System Route / Screen | Status After | DB Tables Written |
|---|---|---|---|
| Registrar views submitted grade entries for verification | `GET /registrar/grades` / `GET /registrar/grades/{grade}` | — | — |
| Registrar finalizes a grade (approves faculty entry) | `POST /registrar/grades/{grade}/finalize` | grade.status = `finalized` | `grades` |
| Registrar bulk-finalizes all grades for a section/quarter | `POST /registrar/grades/finalize-bulk` | grade.status = `finalized` | `grades` |
| Registrar locks grades (no further changes by faculty) | `POST /registrar/grades/{grade}/lock` | grade.status = `locked` | `grades` |
| Registrar locks all grades for a section at once | `POST /registrar/grade-lock/{sectionSubject}/lock` or `POST /registrar/grade-lock/lock-all` | grade.status = `locked` | `grades` |
| Faculty requests a grade unlock (to correct an error) | `POST /faculty/classes/{sectionSubject}/request-unlock` | grade_unlock_request.status = `pending` | `grade_unlock_requests` |
| Registrar approves unlock request | `POST /registrar/grade-lock/unlock-requests/{unlockRequest}/approve` | grade_unlock_request.status = `approved`; grade.status = `finalized` | `grade_unlock_requests`, `grades` |
| Registrar denies unlock request | `POST /registrar/grade-lock/unlock-requests/{unlockRequest}/deny` | grade_unlock_request.status = `denied` | `grade_unlock_requests` |
| Student files a grade complaint | `POST /complaints` | grade_complaint.status = `pending` | `grade_complaints` |
| Staff responds to grade complaint | `PATCH /complaints/{complaint}/respond` | grade_complaint.status = `resolved` / `dismissed` | `grade_complaints` |
| Student downloads their report card | `GET /report-card/{student}/download` (auth required) | — | `report_card_tokens` |
| Third party verifies report card authenticity | `GET /verify/{token}` (public) | — | — (read only) |
| Registrar generates DepEd SF Forms (SF1, SF2, SF9, SF10) | `GET /sf-forms/sf1` / `sf2` / `sf9` / `sf10` | — | — (read only) |
| `CONFIRM WITH SCHOOL` — Who receives the finalized SF Forms? Printed and signed? | ⚠️ NO IMPLEMENTATION (system generates the form view but does not track distribution/signing) | — | — |

---

## Phase 5 — Year-End
*(Quarter/year closes; students are promoted or retained)*

| SOP Step | System Route / Screen | Status After | DB Tables Written |
|---|---|---|---|
| `CONFIRM WITH SCHOOL` — What is the school's promotion criteria? (e.g., minimum passing grade per subject, attendance threshold?) | ⚠️ NO IMPLEMENTATION (promotion criteria thresholds not stored; must be confirmed and then configured) | — | — |
| Registrar reviews aggregate academic reports (honor roll, intervention list) | `GET /registrar/reports/aggregate` | — | — (read only) |
| Registrar runs quarter/section-level assessment finalization | `POST /registrar/assessment/finalize-quarter` / `finalize-section` | grades locked by section | `grades` |
| Registrar locks an assessment section | `POST /registrar/assessment/lock-section` | — | `grades` |
| `CONFIRM WITH SCHOOL` — Does the school issue formal promotion slips or conduct a promotion committee meeting? | ⚠️ NO IMPLEMENTATION | — | — |
| Registrar promotes eligible students to next grade level | `GET /registrar/promotion` → `POST /registrar/promotion` | new enrollment record created for next year | `enrollments` *(exact behavior unverified — confirm with PromotionController)* |
| `CONFIRM WITH SCHOOL` — Are retained students re-enrolled with the same grade level? | `CONFIRM WITH SCHOOL` — PromotionController behavior for retained students is unverified | — | — |
| Admin deactivates the completed academic year | `PATCH /admin/academic-years/{academicYear}/toggle` | academic_year.status = `inactive` | `academic_years` |

---

## System Features With No Clear SOP Basis
*(These exist in the system but the corresponding school procedure is unverified or purely operational)*

| System Feature | Route / Screen | Notes |
|---|---|---|
| LRN encryption at rest | Automatic (Applicant & User model mutators) | Security control; not an SOP step. Run `php artisan lrn:backfill` after deploy |
| Audit log of all sensitive actions | `GET /admin/audit` | Compliance/security; not an SOP step |
| Threat monitoring & flagged logins | `GET /admin/threats` | Security; not an SOP step |
| Compliance export | `GET /admin/compliance` / `export` | Not an SOP step |
| Faculty leave requests | `POST /faculty/leave` → `PATCH /admin/leave/{leaveRequest}/review` | HR function — `CONFIRM WITH SCHOOL` whether this is tracked separately in HRIS |
| Student document requests (TOR, diplomas, etc.) | `POST /documents` → `PATCH /registrar/documents/{id}/status` | `CONFIRM WITH SCHOOL` — is there a formal request form in the office? |
| Faculty–student messaging | `/faculty/inbox`, `/student/inbox` | `CONFIRM WITH SCHOOL` — is this a primary communication channel? |
| School calendar events | `POST /calendar` | `CONFIRM WITH SCHOOL` — who is responsible for publishing calendar entries? |
| Student import via CSV | `POST /admin/students/import` | Operational tool; bypasses normal admissions SOP. Use only for batch-migrating existing students |
| Analytics dashboard | `GET /analytics` | Reporting tool; no SOP step |
| Academic holds view | `GET /student/academic-holds` | `CONFIRM WITH SCHOOL` — what triggers an academic hold? Is it linked to payment or attendance? |

---

## Open Questions for School Confirmation

1. **Entrance test vs. Guidance testing** — Are these the same event or two separate sessions? The system has two separate routes (`/admin/entrance-tests` and `/admin/guidance-testing`). Clarify which route is actually used, or if both are needed.
2. **Payment timing** — Does payment need to be confirmed before enrollment finalization, or can the student be enrolled first and pay later?
3. **Promotion criteria** — What grade average or subject-pass threshold determines promotion vs. retention? This needs to be encoded in the system (currently not stored).
4. **Document checklist** — What is the exact list of required admission documents (birth certificate, Form 137, Good Moral, 2×2 photo, etc.)? This drives the `applicant_requirement_checks` table.
5. **Retained students** — Are retained students re-enrolled automatically, or do they re-apply through the admissions process?
6. **Academic holds** — What conditions trigger an academic hold that blocks a student from seeing their record?
7. **SF Form distribution** — After generating SF Forms, is there a sign-off process that should be tracked in the system?
8. **Grade computation formula** — DepEd 40-40-20 (Written Works / Performance Tasks / Quarterly Assessment) is the system default. Confirm this matches the school's grading policy. Custom per-subject weights can be set via `POST /admin/subjects`.
