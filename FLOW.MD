# Task: Implement Safe Patient Record Linking for Appointment Requests

## Context
We have a clinic management system with:
- patient portal accounts
- clinic patient records
- appointment requests
- official appointments

Right now, patient portal accounts are separate from official clinic patient records.

We want to implement a **safe patient record linking flow** for appointment requests, where clinic staff can review a request and either:

1. link it to an existing patient record, or
2. create a new patient record if no match exists

## Important Rules
- Do NOT automatically link by email only
- Do NOT automatically link by name only
- Do NOT automatically create a new patient record for every request
- Staff must remain the final decision-maker
- Official appointments must always be linked to a valid patient record
- Portal accounts are not the official medical identity
- Linking must avoid duplicate patient records and privacy mistakes

## Goal
Build a workflow where appointment requests from:
- guest users
- logged-in portal users

can be reviewed by clinic staff before becoming official appointments.

## Required Flow

### 1. Appointment Request Creation
When a guest or logged-in user books an appointment:
- create an appointment request only
- do not create the official appointment yet
- do not create a patient record immediately

The request should store:
- full name
- phone number
- email
- preferred date
- preferred time
- requested service
- request status = pending

### 2. Matching Assistance
When staff opens the request, the system should show possible matching patient records based on:
- full name
- birthdate if available
- mobile number
- email

This should only be a **suggestion system**, not an automatic final link.

### 3. Staff Decision
Staff must be able to choose one of the following:

#### Option A: Link to Existing Patient
- select an existing patient record
- create the official appointment under that patient

#### Option B: Create New Patient
- create a new patient record from the request details
- then create the official appointment under that new patient

### 4. Final Appointment Creation
Only after staff review:
- create the official appointment
- official appointment must be linked to the selected or newly created patient record

## Required Features

### A. Patient Match Suggestions
Build a helper function that searches for possible existing patients using:
- exact mobile match
- exact email match
- similar full name
- full name + birthdate combination if available

### B. Staff Review UI
On the admin/staff side, when opening a pending appointment request, show:
- request details
- possible patient matches
- button: Link to Existing Patient
- button: Create New Patient
- button: Reject / Cancel Request

### C. Duplicate Prevention
Before creating a new patient record, show warnings if:
- same mobile already exists
- same email already exists
- same full name + birthdate already exists

### D. Audit / Logging
Log the following actions:
- request approved
- linked to existing patient
- new patient created from request
- official appointment created

## Expected Output
Please provide:

1. recommended database changes if needed
2. backend logic flow
3. controller/service logic
4. matching algorithm suggestion
5. admin UI flow for review and linking
6. duplicate prevention rules
7. sample pseudocode or implementation example

## Design Principle
The final design must prioritize:
- data integrity
- privacy protection
- prevention of duplicate patient records
- staff-controlled verification