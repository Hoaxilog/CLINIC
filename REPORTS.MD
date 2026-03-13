# Codex Context Guide: Filament Reports for Dental Clinic System

## Project Overview

You are working on a **Dental Clinic Management System** built with:

* **Laravel**
* **Filament Admin Panel**
* **MySQL**

The system currently focuses on two core modules:

1. **Patient Records**
2. **Appointments**

The task is to build a **Reports / Analytics module** for the admin side using **Filament pages, widgets, and tables**.

---

## Main Goal

Create a professional **Reports page** in Filament that gives the admin a clear view of:

* patient growth
* appointment flow
* cancellations
* completed appointments
* service demand
* walk-in vs scheduled behavior
* patient demographics

This should look like a real admin analytics page, not just a simple table.

---

## Database Context

The existing database already contains enough data for reporting.

### Patients table

Expected useful fields:

* `id`
* `first_name`
* `middle_name`
* `last_name`
* `birth_date`
* `gender`
* `mobile_number`
* `created_at`

Use this table for:

* total patient count
* patient registration trend
* gender distribution
* age-based reporting
* patient registration report table

### Appointments table

Expected useful fields:

* `id`
* `patient_id`
* `service_id`
* `appointment_date`
* `status`
* `created_at`

Appointment statuses include:

* `Scheduled`
* `Arrived`
* `Ongoing`
* `Completed`
* `Cancelled`

Use this table for:

* total appointments
* appointment status chart
* daily / monthly appointment trend
* completed appointments report
* cancelled appointments report
* service utilization report

### Activity Log table

The system also stores activity logs for actions such as:

* `patient_created`
* `patient_updated`
* `appointment_created`
* `appointment_updated`
* `appointment_cancelled`
* walk-in related events like **Registered Walk-In (Waiting Room)**

Use this table when needed for:

* walk-in analytics
* activity-based clinic reporting

---

## Filament Architecture to Build

Create a main Filament page for reports.

### Suggested file

`app/Filament/Pages/Reports.php`

This page should serve as the main analytics dashboard.

Use Filament widgets for the charts and summary cards.

---

## Required Reports Page Layout

### Top Section: KPI Cards

Use **StatsOverviewWidget**.

Show these 4 cards:

1. **Total Patients**
2. **Total Appointments**
3. **Completed Appointments**
4. **Cancelled Appointments**

These cards should be shown first at the top of the page.

---

### Row 1: Main Summary Charts

#### 1. Appointment Status Distribution

* **Widget type:** `ChartWidget`
* **Chart type:** `pie`
* **Placement:** top row, left
* **Source:** `appointments.status`

Display counts for:

* Scheduled
* Arrived
* Ongoing
* Completed
* Cancelled

Purpose:
Give the admin a quick overview of clinic appointment outcomes.

#### 2. Monthly Patient Registration Trend

* **Widget type:** `ChartWidget`
* **Chart type:** `line`
* **Placement:** top row, right
* **Source:** `patients.created_at`

Display the number of registered patients per month.

Purpose:
Show patient growth over time.

---

### Row 2: Operational Charts

#### 3. Appointment Trend

* **Widget type:** `ChartWidget`
* **Chart type:** `bar`
* **Placement:** second row, left
* **Source:** `appointments.appointment_date`

Display number of appointments by day, week, or month depending on the selected filter.

Purpose:
Show busy and slow clinic periods.

#### 4. Most Requested Services

* **Widget type:** `ChartWidget`
* **Chart type:** `bar`
* **Placement:** second row, right
* **Preferred style:** horizontal bar if practical
* **Source:** `appointments.service_id`

Display the most requested dental services based on appointment count.

Purpose:
Help admin identify which treatments are most in demand.

---

### Row 3: Additional Charts

#### 5. Walk-In vs Scheduled

* **Widget type:** `ChartWidget`
* **Chart type:** `pie`
* **Placement:** third row, left
* **Source:** activity logs and/or appointment flow

Display:

* Walk-In
* Scheduled

Purpose:
Show patient booking behavior.

#### 6. Patient Gender Distribution

* **Widget type:** `ChartWidget`
* **Chart type:** `pie`
* **Placement:** third row, right
* **Source:** `patients.gender`

Display:

* Male
* Female
* Optional: Unspecified

Purpose:
Provide a simple demographic breakdown.

---

## Detailed Report Tables

Below the charts, add detailed report tables using Filament tables.

These may be placed in tabs, sections, or separate report widgets.

### 1. Patient Registration Report

Columns:

* Patient ID
* Full Name
* Gender
* Birth Date
* Mobile Number
* Date Registered

Filters:

* date range
* gender

Purpose:
Formal patient registration reporting.

### 2. Appointment Report

Columns:

* Appointment ID
* Patient Name
* Service
* Appointment Date
* Status

Filters:

* date range
* status
* service

Purpose:
Main appointment reporting table.

### 3. Completed Appointment Report

Columns:

* Appointment ID
* Patient Name
* Service
* Appointment Date
* Status

Default filter:

* `status = Completed`

Purpose:
Track finished treatments.

### 4. Cancelled Appointment Report

Columns:

* Appointment ID
* Patient Name
* Service
* Appointment Date
* Status

Default filter:

* `status = Cancelled`

Purpose:
Track cancellations.

---

## Filters to Support

At minimum, the reports page should support:

* Today
* This Week
* This Month
* This Year
* Custom Date Range

These filters should affect both summary widgets and charts whenever reasonable.

---

## Design Rules

The page should look:

* clean
* formal
* modern
* admin-focused
* readable
* responsive

Do not overload the page with too many metrics.

Prioritize the most useful reporting data first.

---

## Priority Order

If building step by step, follow this order:

1. Reports page
2. StatsOverviewWidget
3. Appointment Status pie chart
4. Monthly Patient Registration line chart
5. Appointment Trend bar chart
6. Most Requested Services bar chart
7. Walk-In vs Scheduled pie chart
8. Gender Distribution pie chart
9. Detailed report tables

---

## Implementation Notes for Codex

When generating code:

* use Filament conventions
* keep widget classes separate when appropriate
* use Eloquent queries
* keep logic readable
* avoid overengineering
* assume the system already has `Patient`, `Appointment`, and possibly `ActivityLog` models
* join related tables when needed for patient names and service labels
* use safe null handling for incomplete patient records

If a `services` table exists, use it to resolve `service_id` into service names.
If not, structure the code so service labels can still be mapped clearly.

---

## Expected Output From Codex

Generate:

* the Filament `Reports` page
* widget classes for all summary cards and charts
* Filament table definitions for the reports
* queries for each metric/chart
* clean layout organization

---

## Short Build Summary

Build a **Filament Reports / Analytics page** for a dental clinic system with:

### KPI Cards

* Total Patients
* Total Appointments
* Completed Appointments
* Cancelled Appointments

### Charts

* Appointment Status — Pie Chart
* Monthly Patient Registration — Line Chart
* Appointment Trend — Bar Chart
* Most Requested Services — Bar Chart
* Walk-In vs Scheduled — Pie Chart
* Patient Gender Distribution — Pie Chart

### Tables

* Patient Registration Report
* Appointment Report
* Completed Appointment Report
* Cancelled Appointment Report

The result should feel like a proper capstone-quality clinic admin reporting module.

---

## Printable Reports (Export / Hard Copy)

The system should also support **printing formal reports** for record keeping, auditing, or submission (e.g., clinic management reports for monthly review or capstone demonstration).

Printable reports should generate a **clean print-friendly layout** separate from the dashboard charts.

### Print Access

Add a **"Generate Report" or "Print Report" button** inside each report table section.

Example placements:

* Patient Registration Report → `Print Patient Report`
* Appointment Report → `Print Appointment Report`
* Completed Appointment Report → `Print Completed Treatments`
* Cancelled Appointment Report → `Print Cancellation Report`

These buttons should open a **print-friendly page or modal**.

---

## Print Layout Structure

Each generated report should contain the following structure:

### Header

Include clinic and report metadata.

Example:

* Clinic Name
* System Name (Dental Clinic Management System)
* Report Title
* Generated By (Admin user)
* Date Generated
* Selected Date Range

Example header layout:

```
CLINIC NAME
Dental Clinic Management System
Report: Appointment Report
Generated: 2026-03-12
Date Range: Jan 1 – Jan 31
```

---

### Report Summary Section

Show quick metrics above the table.

Example summary cards:

* Total Patients
* Total Appointments
* Completed Appointments
* Cancelled Appointments

These should be rendered as **simple text metrics** in the printable version (no charts required).

---

### Data Table

Include the detailed report table used in Filament.

Example for Appointment Report:

Columns:

* Appointment ID
* Patient Name
* Service
* Appointment Date
* Status

Example for Patient Report:

Columns:

* Patient ID
* Full Name
* Gender
* Birth Date
* Mobile Number
* Date Registered

The table should be **clean and formatted for printing**.

---

### Footer

Include simple footer metadata.

Example:

```
Generated by Dental Clinic Management System
Page 1 of X
```

Optional:

* Signature line for clinic administrator
* Clinic contact information

---

## Filament Implementation Suggestion

Add a **"Print" action** to each Filament table using a custom action.

Example concept:

* Table Action → `Print`
* Route → `/reports/print/{reportType}`
* Controller generates printable blade view

Example blade file:

```
resources/views/reports/print/appointment-report.blade.php
resources/views/reports/print/patient-report.blade.php
```

These views should:

* load the filtered report data
* apply minimal styling
* trigger `window.print()` if desired

---

## Print Styling Rules

Use simple print CSS:

* white background
* black text
* minimal borders
* table gridlines
* avoid heavy colors

Example print CSS rules:

* hide navigation
* hide Filament sidebar
* expand table width

---

## Example Printable Reports to Support

1. Patient Registration Report
2. Appointment Report
3. Completed Treatments Report
4. Cancelled Appointment Report
5. Service Utilization Summary

These reports should allow the admin to **generate a printable document copy of clinic analytics or records**.

---

## Updated Build Summary

The system should provide:

### KPI Cards

* Total Patients
* Total Appointments
* Completed Appointments
* Cancelled Appointments

### Charts

* Appointment Status — Pie Chart
* Monthly Patient Registration — Line Chart
* Appointment Trend — Bar Chart
* Most Requested Services — Bar Chart
* Walk-In vs Scheduled — Pie Chart
* Patient Gender Distribution — Pie Chart

### Tables

* Patient Registration Report
* Appointment Report
* Completed Appointment Report
* Cancelled Appointment Report

### Printable Reports

* Print Patient Report
* Print Appointment Report
* Print Completed Treatments Report
* Print Cancellation Report

The printable reports should produce **clean formatted documents suitable for administrative records or presentation.**

---

## Reports Navigation Structure (Subsections)

Instead of putting all analytics and reports inside one single page, the **Reports module should be divided into subsections** in the Filament sidebar.

This improves:

* usability
* performance (fewer widgets loading at once)
* organization of analytics

### Recommended Sidebar Structure

```
Reports
 ├── Overview
 ├── Patient Reports
 ├── Appointment Reports
 └── Printable Reports
```

Each subsection should be implemented as a **separate Filament Page**.

Example structure:

```
app/Filament/Pages/Reports/

ReportsOverview.php
PatientReports.php
AppointmentReports.php
PrintableReports.php
```

---

## Reports Overview Page

Purpose: provide **high‑level clinic analytics**.

This page should contain only **widgets and charts**, not large data tables.

### Widgets

StatsOverviewWidget:

* Total Patients
* Total Appointments
* Completed Appointments
* Cancelled Appointments

### Charts

* Appointment Status Distribution — Pie Chart
* Monthly Patient Registration — Line Chart
* Appointment Trend — Bar Chart

This page answers:

"How is the clinic performing overall?"

---

## Patient Reports Page

Purpose: show analytics related to patient demographics and registration.

### Charts

* Patient Gender Distribution — Pie Chart
* Patient Age Distribution — Bar Chart
* Patient Registration Trend — Line Chart

### Tables

Patient Registration Report

Columns:

* Patient ID
* Full Name
* Gender
* Birth Date
* Mobile Number
* Date Registered

### Filters

* Date Range
* Gender

### Actions

* Print Patient Report

This page answers:

"Who are the clinic's patients and how are they growing over time?"

---

## Appointment Reports Page

Purpose: analyze appointment performance and clinic workload.

### Charts

* Appointment Status Distribution — Pie Chart
* Walk‑In vs Scheduled — Pie Chart
* Appointments Per Day / Month — Bar Chart

### Tables

Appointment Report

Columns:

* Appointment ID
* Patient Name
* Service
* Appointment Date
* Status

Additional filtered tables:

Completed Appointments
Cancelled Appointments

### Filters

* Date Range
* Appointment Status
* Service

### Actions

* Print Appointment Report
* Print Completed Treatments Report
* Print Cancellation Report

This page answers:

"How are appointments performing in the clinic?"

---

## Printable Reports Page

Purpose: allow administrators to **generate formal printable reports**.

This page focuses on report generation instead of analytics widgets.

### Available Reports

* Patient Registration Report
* Appointment Report
* Completed Treatments Report
* Cancelled Appointment Report
* Monthly Clinic Summary

Each report should allow:

* selecting a date range
* previewing the report
* printing the report

### Example Workflow

```
Select Report Type
        ↓
Choose Date Range
        ↓
Generate Report
        ↓
Preview Printable Layout
        ↓
Print / Save as PDF
```

---

## Final Recommended Reports Module

```
Reports
├── Overview
│   ├── KPI Stats
│   ├── Appointment Status Chart
│   └── Patient Growth Chart
│
├── Patient Reports
│   ├── Gender Distribution
│   ├── Age Distribution
│   └── Patient Registration Table
│
├── Appointment Reports
│   ├── Appointment Status Chart
│   ├── Walk‑In vs Scheduled Chart
│   ├── Appointment Trend Chart
│   └── Appointment Tables
│
└── Printable Reports
    ├── Print Patient Report
    ├── Print Appointment Report
    ├── Print Completed Treatments
    └── Print Cancellation Report
```

This structure keeps the Reports module **organized, scalable, and easier to use inside the Filament admin panel**.
