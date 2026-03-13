# Filament Dashboard Layout Guide (Updated with Visual Wireframe)

Dental Clinic Management System

This guide defines the **final recommended dashboard layout** so the
interface feels balanced, readable, and operational without duplicating
the Reports module.

------------------------------------------------------------------------

# Dashboard Philosophy

Dashboard = **Today's Operations + Quick Overview**\
Reports = **Analytics & Historical Data**

The dashboard should answer:

• What is happening **today**?\
• What needs **attention now**?\
• What actions can staff perform **quickly**?

------------------------------------------------------------------------

# Visual Hierarchy

To avoid awkward layouts, the dashboard should follow **three layers of
importance**.

1️⃣ Overview (stats)\
2️⃣ Today's workflow (appointments & approvals)\
3️⃣ System status & shortcuts

------------------------------------------------------------------------

# Final Recommended Dashboard Layout

## Row 1 --- Overview Stats

Four summary cards:

-   Total Patients
-   Today's Appointments
-   Waiting / Arrived Patients
-   Pending Appointment Requests

Example layout:

| Total Patients \| Today's Appointments \| Waiting / Arrived \| Pending
  Requests \|

Optional metrics:

-   Completed Today
-   Cancelled Today

------------------------------------------------------------------------

# Row 2 --- Today's Workflow

Two main operational widgets.

| Pending Appointment Requests \| Today's Appointment Schedule \|

### Pending Appointment Requests

Purpose: allow staff to **approve requests quickly**.

Columns:

-   Patient
-   Appointment Date
-   Service
-   Status

Actions:

-   Approve
-   Reject
-   View All Requests

Limit results to **5 records**.

------------------------------------------------------------------------

### Today's Appointment Schedule

Purpose: monitor **today's clinic schedule**.

Columns:

-   Time
-   Patient Name
-   Service
-   Status

Only display **appointments scheduled today**.

------------------------------------------------------------------------

# Row 3 --- Clinic Flow + Status Chart

| Lobby Flow Snapshot \| Appointment Status Today \|

### Lobby Flow Snapshot

Compact clinic flow statistics.

Metrics:

-   Waiting
-   Arrived
-   Ongoing
-   Completed

Example:

Waiting: 3\
Arrived: 5\
Ongoing: 2\
Completed: 8

Include button:

View Full Lobby Flow

------------------------------------------------------------------------

### Appointment Status Today Chart

Chart type: **Pie or Doughnut**

Categories:

-   Scheduled
-   Waiting
-   Arrived
-   Ongoing
-   Completed
-   Cancelled

If there are no appointments today, show:

"No appointments recorded today"

This avoids empty chart visuals.

------------------------------------------------------------------------

# Row 4 --- Activity + Quick Actions

| Recent Activity \| Quick Actions \|

### Recent Activity

Displays latest system actions.

Source: **Activity Logs**

Columns:

-   User
-   Activity
-   Date / Time

Limit to **5 records** to prevent the table from dominating the page.

Example activities:

-   Created Appointment
-   Updated Appointment Status
-   Registered Patient
-   Admitted Patient to Chair

------------------------------------------------------------------------

### Quick Actions

Buttons for frequent tasks.

Recommended actions:

-   Add Patient
-   Book Appointment
-   Register Walk-In Patient
-   View Today's Schedule
-   Generate Reports

Example layout:

\[ Add Patient \] \[ Book Appointment \]\
\[ Register Walk‑In \] \[ Today's Schedule \]\
\[ Generate Reports \]

Use icons where possible.

Limit quick actions to **5 buttons**.

------------------------------------------------------------------------

# Visual Wireframe

Below is the recommended **dashboard visual structure**.

    ┌──────────────────────────────────────────────────────────────┐
    │                          ROW 1                               │
    │                     SUMMARY STATS                            │
    │                                                              │
    │  [ Total Patients ] [ Today's Appts ] [ Waiting ] [ Requests ]│
    │                                                              │
    └──────────────────────────────────────────────────────────────┘


    ┌───────────────────────────────┬───────────────────────────────┐
    │           ROW 2               │           ROW 2               │
    │                               │                               │
    │   Pending Appointment         │   Today's Appointment         │
    │   Requests                    │   Schedule                    │
    │                               │                               │
    │   Patient | Date | Status     │   Time | Patient | Status     │
    │                               │                               │
    │   Approve / Reject            │                               │
    │                               │                               │
    └───────────────────────────────┴───────────────────────────────┘


    ┌───────────────────────────────┬───────────────────────────────┐
    │           ROW 3               │           ROW 3               │
    │                               │                               │
    │       Lobby Flow Snapshot     │     Appointment Status        │
    │                               │           Today Chart         │
    │                               │                               │
    │   Waiting     0               │        Pie / Doughnut         │
    │   Arrived     0               │                               │
    │   Ongoing     0               │                               │
    │   Completed   0               │                               │
    │                               │                               │
    └───────────────────────────────┴───────────────────────────────┘


    ┌───────────────────────────────┬───────────────────────────────┐
    │           ROW 4               │           ROW 4               │
    │                               │                               │
    │         Recent Activity       │          Quick Actions        │
    │                               │                               │
    │   User | Activity | Time      │   [+ Add Patient]             │
    │                               │   [📅 Book Appointment]        │
    │                               │   [🚶 Register Walk‑In]        │
    │                               │   [📊 Generate Reports]        │
    │                               │                               │
    └───────────────────────────────┴───────────────────────────────┘

------------------------------------------------------------------------

# UI Improvements

To avoid awkward layouts:

✔ Keep widgets in **balanced two‑column rows**\
✔ Avoid empty charts\
✔ Limit table heights\
✔ Convert quick actions to **buttons instead of form inputs**\
✔ Maintain clear **visual hierarchy**

------------------------------------------------------------------------

# What NOT to Place on the Dashboard

Avoid duplicating report data.

Do not include:

-   Patient demographic charts
-   Service utilization analytics
-   Monthly growth analytics
-   Printable reports
-   Full patient tables
-   Full appointment management tables

Those belong in the **Reports module**.

------------------------------------------------------------------------

# Result

Following this layout produces a dashboard that is:

✔ Balanced\
✔ Easy to scan\
✔ Focused on daily clinic operations\
✔ Non‑redundant with Reports\
✔ Professional and production‑like
