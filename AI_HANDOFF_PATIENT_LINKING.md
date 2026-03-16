# AI Handoff: Patient Account vs Patient Record Flow

## Date

- 2026-03-16

## Goal of Recent Changes

- Stop auto-linking portal user accounts to clinic patient records at booking time.
- Keep booking as request-first (`Pending`) and only attach/create clinic patient record at admin approval.

## Key Design Decision

- Portal account identity and clinic patient record identity are separated.
- Booking request stores requester details in `appointments` first.
- Clinic-facing record linkage happens only when staff approves.

## What Was Implemented

### 1) Request-first schema support

- Manual SQL patch available for phpMyAdmin/XAMPP:
    - `database/manual_sql/2026_03_15_appointments_requester_fields.sql`
- (Legacy reference only) migration file also exists:
    - `database/migrations/2026_03_15_000005_update_appointments_for_requester_fields.php`
- Schema changes:
    - `appointments.patient_id` becomes nullable.
    - Added requester columns:
        - `requester_user_id`
        - `requester_first_name`
        - `requester_last_name`
        - `requester_contact_number`
        - `requester_email`
    - Added indexes on requester fields where appropriate.

### 2) Booking flow no longer creates/updates `patients` on submit

- Updated:
    - `app/Livewire/appointment/BookAppointment.php`
- Behavior now:
    - Booking request inserts into `appointments` with:
        - `patient_id = null`
        - requester fields filled
        - status defaults to `Pending` (with enum fallback logic)
    - Duplicate active request check now uses requester identity (`requester_user_id` / `requester_email`) instead of `patient_id`.
    - Logged-in account prefill added for `email`, `contact_number`, and split username -> first/last name best effort.

### 3) Pending approvals can render requests without linked patient

- Updated:
    - `app/Livewire/PendingApprovalsWidget.php`
    - `app/Livewire/AppointmentCalendar.php` (pending approval sections)
- Queries changed from inner join to left join on `patients` in pending/email paths.
- Uses SQL `COALESCE(patients.*, appointments.requester_*)` for names/contact/email.

### 4) Approval now links/creates patient record before scheduling

- Updated approval logic in both admin entry points:
    - `app/Livewire/PendingApprovalsWidget.php`
    - `app/Livewire/AppointmentCalendar.php`
- On approve to `Scheduled`:
    - If `patient_id` already exists, keep it.
    - Else try to find existing patient by exact `(email + first_name + last_name)`.
    - If not found, create new `patients` row from requester info.
    - Update appointment with resolved `patient_id`.
- This ensures approved appointment appears in calendar views that join on `patients`.

### 5) Patient profile wording adjusted

- Updated:
    - `resources/views/patient/profile.blade.php`
- Changes:
    - Label changed to emphasize clinic record linkage by staff.
    - Account email shown as account email (not mixed fallback from clinic record email).

### 6) Guest booking re-enabled (without restoring patient auto-link)

- Updated:
    - `routes/web.php`
    - `app/Livewire/appointment/BookAppointment.php`
- Changes:
    - `/book` route moved to public routes (no `auth` middleware).
    - Removed forced login redirect in `BookAppointment::mount()`.
    - Guest CAPTCHA remains required in submit flow.
    - Fixed guest CAPTCHA required-message text.
    - Duplicate active-request check hardened:
        - logged-in: `requester_user_id` and/or `requester_email`
        - guest: `requester_email`

### 7) Feature test coverage added for guest booking

- Added:
    - `tests/Feature/GuestBookingAccessTest.php`
- Coverage:
    - `/book` route exists and is public.
    - Guest submit requires CAPTCHA token.
    - Guest submit with successful CAPTCHA inserts request-first appointment:
        - `patient_id = null`
        - `status = Pending`
        - requester fields populated
        - `modified_by = GUEST`

## Current Runtime Behavior

### Logged-in booking

1. User submits booking request.
2. Appointment saved as `Pending` with requester fields and null `patient_id`.
3. Admin approves.
4. System links or creates patient record and writes `patient_id`.
5. Appointment becomes `Scheduled` and is visible in calendar.

### Guest booking (current)

- Guest can access `/book` and submit request-first appointments.
- CAPTCHA is enforced for unauthenticated submits.
- Submitted request remains unlinked to clinic patient record until admin approval.

## Open Item / Next Likely Task

- Do a live manual QA pass on real MySQL data for end-to-end guest flow:
    1. Guest submit -> `Pending`, `patient_id` null, requester fields saved.
    2. Admin pending list renders guest requester info.
    3. Approve to `Scheduled` -> patient linked/created and `patient_id` written.
    4. Scheduled item appears in calendar.

## Important Notes for Next AI

- The workspace has many unrelated UI/build changes in progress (generated assets and page redesigns). Avoid reverting unrelated files.
- There is existing static-analysis noise unrelated to this feature (e.g., Livewire view `layout()` typing warning).
- User workflow preference: do not rely on `php artisan migrate`; provide manual SQL scripts for XAMPP/phpMyAdmin schema updates.

## Minimal Verification Checklist

1. Create booking request while logged in -> status `Pending`, `patient_id` null, requester fields populated.
2. Open admin pending approvals -> request visible with requester name/contact/email.
3. Approve request -> `patient_id` populated, status `Scheduled`.
4. Confirm scheduled item appears in calendar slot.
5. Reject path still sends status update email and does not require patient linkage.

## Latest Confirmed Answers (Conversation)

- Q: "After approve, does it create patient records?"
    - A: Yes. On `Scheduled` approval, the system links existing patient (exact email + first + last) or creates a new `patients` row, then writes `appointments.patient_id`.

- Q: "What if patient books in guest mode?"
    - A: Guest mode is enabled on `/book`.
    - CAPTCHA is required for guest submit.
    - Booking remains request-first and does not auto-link to `patients` until admin approval.

## New Chat Starter (Copy/Paste)

Use this in a new chat to continue quickly:

"Continue from `AI_HANDOFF_PATIENT_LINKING.md`. Keep account-to-patient unlinking at booking time. Current flow is request-first (`Pending`) with requester fields; patient record is linked/created only on approval to `Scheduled`. Next task: re-enable true guest booking without restoring auto-link at booking. Implement guest access in `BookAppointment`, keep captcha for guests, keep admin approval-time patient linking, then validate pending -> approve -> calendar visibility. Do not revert unrelated UI/build changes in workspace."

"Current status update: guest booking is already re-enabled and covered by `tests/Feature/GuestBookingAccessTest.php`; continue with live MySQL manual QA for pending -> approve -> calendar verification."
