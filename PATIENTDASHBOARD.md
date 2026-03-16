# Patient Portal Dashboard – UI Structure Proposal

## Overview

This document proposes a **clean and professional dashboard layout** for the Patient Portal.
The dashboard focuses on **appointment management, communication, and patient convenience**.

Because the portal account is **separate from official clinic patient records**, the dashboard intentionally avoids displaying medical information such as treatment history, dental charts, or prescriptions.

The dashboard is designed to be:

* simple
* easy to navigate
* useful for patients
* clear during system demonstration or defense

---

# Dashboard Layout Structure

The dashboard should be divided into clear sections:

```
----------------------------------------------------
Top Navigation Bar
----------------------------------------------------
Logo | Dashboard | Appointments | Profile | Logout
----------------------------------------------------

Main Dashboard Area
----------------------------------------------------
Welcome Section
Quick Actions
Upcoming Appointments
Notifications
Clinic Information
----------------------------------------------------
```

---

# 1. Welcome Section

The dashboard should greet the user after login.

Example:

```
Welcome back, Ana Cruz!
Manage your appointments and stay updated with clinic notifications.
```

This creates a more personal and professional experience.

---

# 2. Quick Actions Panel

This section allows patients to perform important actions quickly.

Example:

```
Quick Actions
--------------------------------
[ Book Appointment ]
[ View Appointments ]
[ Update Profile ]
```

The **Book Appointment** button should be the most visible action.

---

# 3. Upcoming Appointments

Patients should immediately see their upcoming appointments or appointment requests.

Example:

```
Upcoming Appointments
--------------------------------
June 20 | 10:00 AM | Dental Cleaning | Confirmed
July 5  | 2:00 PM  | Consultation    | Pending
```

Possible actions:

```
[Cancel Appointment]
[Request Reschedule]
```

All modifications should still require **staff approval**.

---

# 4. Appointment History

Patients can view their previous appointment requests made through the portal.

Example:

```
Appointment History
--------------------------------
May 12 | Consultation | Completed
April 3 | Tooth Filling | Cancelled
```

Note:
Only portal-generated appointment requests are displayed.

---

# 5. Notifications Panel

Notifications help inform the patient about appointment updates.

Example:

```
Notifications
--------------------------------
✔ Your appointment for June 20 has been confirmed.
✔ Your appointment request for July 5 is pending review.
✔ Reminder: You have an appointment tomorrow at 10:00 AM.
```

Notifications help reduce missed appointments.

---

# 6. Patient Profile Summary

A small profile section can show the patient’s portal information.

Example:

```
Profile Information
--------------------------------
Name: Ana Cruz
Email: ana@email.com
Phone: 09171234567

[Edit Profile]
[Change Password]
```

Patients can update their contact details through the **Profile page**.

---

# 7. Clinic Information Section

Providing clinic information helps patients quickly find important details.

Example:

```
Clinic Information
--------------------------------
Opening Hours: Monday – Saturday | 9:00 AM – 6:00 PM
Phone Number: 0917-123-4567
Address: Quezon City
```

Optional additions:

* clinic map
* emergency contact number

---

# Dashboard Example Layout

```
----------------------------------------------------
Welcome, Ana Cruz
----------------------------------------------------

[ Book Appointment ]

Upcoming Appointments
--------------------------------
June 20 | 10:00 AM | Confirmed

Notifications
--------------------------------
✔ Appointment Confirmed

Quick Actions
--------------------------------
View Appointments
Update Profile

Clinic Information
--------------------------------
Mon–Sat 9AM–6PM
Phone: 0917-123-4567
```

---

# Security and Privacy Considerations

The patient dashboard **does not display medical records**, including:

* dental charts
* treatment history
* prescriptions
* diagnosis notes

These records remain accessible only to **authorized clinic staff**.

This design protects patient privacy and prevents incorrect linking between portal accounts and clinic patient records.

---

# Summary

The patient dashboard provides useful features focused on:

* appointment booking
* request tracking
* notifications
* profile management
* clinic communication

By separating portal functions from medical records, the system maintains **data integrity, privacy, and a simple user experience**.

---
