# Guest Mode QA Checklist (Manual Sign-off)

## Scope

Validate guest booking flow end-to-end using request-first behavior:

- booking request is created as Pending
- patient_id is null at submit time
- patient linkage/creation happens only during admin approval

---

## Pre-Test Setup

1. Open app in an incognito/private browser window (not logged in).
2. Ensure at least one row exists in `services`.
3. Ensure reCAPTCHA keys are configured and widget renders on booking page.
4. Choose a future slot that is not blocked and not full.

Evidence required:

- Screenshot of booking page URL and visible form.

---

## A. Guest Access and UI

### A1. Guest can access booking page

Steps:

1. Navigate to `/book` while logged out.

Expected:

- Page loads.
- No redirect to login.

Evidence:

- Screenshot showing `/book` URL and form.

### A2. CAPTCHA visible for guest

Steps:

1. View booking form as guest.

Expected:

- CAPTCHA widget is visible.

Evidence:

- Screenshot showing CAPTCHA area.

### A3. Required fields present

Steps:

1. Inspect form fields.

Expected:

- First Name, Last Name, Contact Number, Email, Service, Date, Time are present.

Evidence:

- Screenshot of full form.

---

## B. Guest Validation

### B1. Submit without CAPTCHA

Steps:

1. Fill all required fields.
2. Do not complete CAPTCHA.
3. Submit.

Expected:

- CAPTCHA validation error appears.
- No new row inserted in `appointments`.

Evidence:

- UI error screenshot.
- DB query result screenshot.

### B2. Missing required field

Steps:

1. Leave one required field empty (example: email).
2. Submit.

Expected:

- Field validation error appears.
- No new row inserted.

Evidence:

- UI error screenshot.
- DB query result screenshot.

### B3. Invalid date/slot

Steps:

1. Attempt past date or invalid slot.
2. Submit.

Expected:

- Booking blocked with validation/unavailable message.
- No new row inserted.

Evidence:

- UI error screenshot.

---

## C. Successful Guest Booking (Core Proof)

### C1. Valid submit with CAPTCHA

Steps:

1. Fill all required fields.
2. Complete CAPTCHA.
3. Submit.

Expected:

- Success message appears.
- Page redirects/refreshes to booking page.

Evidence:

- Success message screenshot.

### C2. Database verification of request-first insert

Expected in `appointments` for new row:

- `patient_id` = NULL
- `status` = Pending
- `requester_user_id` = NULL
- `requester_first_name` populated
- `requester_last_name` populated
- `requester_contact_number` populated
- `requester_email` populated
- `modified_by` = GUEST
- `booking_type` = online_appointment (if column exists)

Suggested SQL:

```sql
SELECT id, appointment_date, status, patient_id, requester_user_id,
       requester_first_name, requester_last_name, requester_contact_number,
       requester_email, modified_by, booking_type
FROM appointments
ORDER BY id DESC
LIMIT 5;
```

Evidence:

- phpMyAdmin result screenshot.

---

## D. Duplicate Active Request Rule (Guest)

### D1. Same guest email cannot create second active request

Steps:

1. Create one valid guest booking.
2. Attempt second booking with same guest email while first is still active (Pending/Scheduled/Waiting/Ongoing).

Expected:

- Second booking is blocked with duplicate active request message.
- No second active row created.

Suggested SQL:

```sql
SELECT id, status, requester_email
FROM appointments
WHERE requester_email = 'guest_email_used'
ORDER BY id DESC;
```

Evidence:

- UI error screenshot.
- SQL result screenshot.

---

## E. Slot Protection

### E1. Full slot blocked

Steps:

1. Attempt booking on a known full slot.

Expected:

- Message indicates slot is full.
- Booking not created.

Evidence:

- UI screenshot.

### E2. Blocked slot blocked

Steps:

1. Attempt booking on blocked slot.

Expected:

- Message indicates slot is unavailable.
- Booking not created.

Evidence:

- UI screenshot.

---

## F. Admin Approval Flow for Guest Request

### F1. Pending list shows guest requester data

Steps:

1. Login as admin/staff.
2. Open pending approvals.

Expected:

- Guest request is visible.
- Name/contact/email shown from requester fields.

Evidence:

- Pending list screenshot.

### F2. Approve request to Scheduled

Steps:

1. Approve selected guest request.

Expected:

- `appointments.status` becomes Scheduled.
- `appointments.patient_id` becomes non-null.
- Existing patient is linked by exact `(email + first_name + last_name)` or a new patient is created.

Suggested SQL:

```sql
SELECT id, status, patient_id, requester_first_name, requester_last_name, requester_email
FROM appointments
WHERE id = APPOINTMENT_ID;

SELECT id, first_name, last_name, email_address, mobile_number
FROM patients
WHERE id = PATIENT_ID_FROM_APPOINTMENT;
```

Evidence:

- UI approval screenshot.
- SQL result screenshots.

### F3. Calendar visibility after approval

Steps:

1. Open appointment calendar/schedule view.

Expected:

- Approved appointment appears in the scheduled slot.

Evidence:

- Calendar screenshot.

---

## G. Reject Path

### G1. Reject pending guest request

Steps:

1. Reject a pending guest request.

Expected:

- `appointments.status` becomes Cancelled.
- Rejection does not require patient linkage.

Suggested SQL:

```sql
SELECT id, status, patient_id
FROM appointments
WHERE id = APPOINTMENT_ID;
```

Evidence:

- UI rejection screenshot.
- SQL result screenshot.

---

## Pass Criteria (Release Gate)

1. Guest can access `/book` without login.
2. CAPTCHA is enforced for guest submit.
3. Valid guest submit creates Pending request-first row with `patient_id = NULL`.
4. Duplicate active request by same guest email is blocked.
5. Admin sees guest request in pending approvals with requester data.
6. Approval links/creates patient and sets `patient_id`, then appears in calendar.
7. Rejection flow works without forced patient linkage.

---

## Sign-off Table

| ID  | Test Case                                         | Result (Pass/Fail) | Evidence Link / Screenshot | Tester | Date |
| --- | ------------------------------------------------- | ------------------ | -------------------------- | ------ | ---- |
| A1  | Guest access to `/book`                           |                    |                            |        |      |
| A2  | CAPTCHA visible for guest                         |                    |                            |        |      |
| A3  | Required fields visible                           |                    |                            |        |      |
| B1  | Submit without CAPTCHA blocked                    |                    |                            |        |      |
| B2  | Required field validation works                   |                    |                            |        |      |
| B3  | Invalid date/slot blocked                         |                    |                            |        |      |
| C1  | Valid guest submit success                        |                    |                            |        |      |
| C2  | Request-first DB row verified                     |                    |                            |        |      |
| D1  | Duplicate active request blocked                  |                    |                            |        |      |
| E1  | Full slot blocked                                 |                    |                            |        |      |
| E2  | Blocked slot blocked                              |                    |                            |        |      |
| F1  | Pending list shows guest requester data           |                    |                            |        |      |
| F2  | Approve sets status + patient_id                  |                    |                            |        |      |
| F3  | Calendar shows approved appointment               |                    |                            |        |      |
| G1  | Reject sets Cancelled without linkage requirement |                    |                            |        |      |
