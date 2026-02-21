# Project Notes (Auto-maintained)

## Overview
- Laravel app for clinic booking/records with Livewire.
- Public home at `/`, login at `/login`.

## Appointment Booking
- Public booking at `/book` (Livewire: `app/Livewire/appointment/BookAppointment.php`).
- Captcha required for guests; token sent via hidden input and verified server-side.
- Slots: default 9:00–17:00, 30-minute increments; capacity 2 per slot.
- Booking creates `Pending` appointment; staff/admin approve -> `Scheduled`.
- Email templates moved under `resources/views/appointment/emails/`.

## Approval Flow
- `Pending Approvals` tab in Appointments for Admin/Staff.
- Approve -> `Scheduled`, Reject -> `Cancelled`.
- Status update email sent on changes.

## Calendar
- Pending hidden from calendar view but still counts toward slot capacity.
- Double-booked slots show single block with `x2` and list popup.

## Patient Records
- Default view is table; cards view optional.
- Right-side details panel removed; full-width table.
- Action menu per row: View Full Record (opens PatientFormModal step 1), Delete for staff/admin.

## Notifications
- Notification bell enhanced:
  - Admin/Staff: pending approvals count, new requests (last 24h), daily summary, next up.
  - Patients: status changes (last 24h), daily summary, next appointment.

## Login
- `/` = home page.
- `/login` redirects to dashboard if already authenticated.
- Captcha only after 3 failed login attempts (session-based counter).

## Email Templates
- `resources/views/appointment/emails/appointment-confirmation.blade.php`
- `resources/views/appointment/emails/appointment-status-update.blade.php`

## Pending Decisions / TODO
- Confirm DB enum includes `Pending` in `appointments.status`.
- Decide if patient table should show full profile columns or a minimal set with row expand.
