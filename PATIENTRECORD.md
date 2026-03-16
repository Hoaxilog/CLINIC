# Design Decision: Separating Patient Accounts from Patient Records

## Overview

During the design of the system, we initially considered linking **patient accounts** directly to **patient medical records**. However, after evaluating the workflow, data integrity, and privacy implications, we decided to **separate the patient portal accounts from the official clinic patient records**.

This document explains the **problems encountered** with the linked approach and the **reason for adopting a separated design**.

---

# Initial Plan

The original design attempted to:

* Create a **patient account**
* Link the account directly to an existing **patient record**
* Allow patients to book appointments using that linked record

This meant the system needed to determine:

> Which patient record belongs to the person creating the account?

---

# Problems Encountered

## 1. Identity Verification Problem

When patients create accounts online, the system cannot reliably verify if the person registering is truly the owner of an existing patient record.

Example scenario:

Two patients exist in the clinic database:

* Clarenz Dela Cruz
* Clarenz Santos

If someone creates an account and enters:

* Name: Clarenz
* Birthdate
* Phone number

The system cannot confidently determine **which patient record belongs to that account**.

Possible risks:

* Linking the account to the wrong patient record
* Allowing access to another patient's information

---

## 2. Privacy and Security Risks

Medical records contain sensitive information such as:

* treatment history
* dental charts
* prescriptions
* diagnosis notes

If an account is incorrectly linked to a patient record, the user may gain access to **another patient's medical data**, which is a serious privacy concern.

Healthcare systems must prioritize protecting patient information.

---

## 3. Duplicate Patient Records

Another issue occurs when:

1. A patient visits the clinic as a **walk-in**
2. Staff creates a **patient record**
3. Later the same patient creates an **online account**

If the system automatically creates another patient record during registration, the result is **duplicate records**.

Example:

Patient records become:

* Patient ID 25 – created during walk-in
* Patient ID 48 – created during online signup

Medical history may then be split across multiple records.

---

## 4. Guest Booking vs Account Booking Conflicts

When the system allows both:

* guest appointment booking
* logged-in appointment booking

The system must decide whether to:

* reuse an existing patient record
* create a new patient record

Without reliable verification, this leads to:

* duplicate records
* staff confusion
* inconsistent patient histories

---

## 5. Increased Administrative Work

To solve the linking problem, staff would need to manually verify and approve account-to-patient linking.

Example workflow:

1. Patient registers online
2. System detects possible matching patient
3. Staff reviews and decides whether to link

This introduces **additional work for clinic staff**, which contradicts the goal of simplifying the workflow.

---

# Design Decision

To avoid these issues, the system adopts the following design:

**Patient accounts and patient medical records are managed separately.**

### Patient Account (Portal)

Used only for:

* logging in
* submitting appointment requests
* viewing appointment requests
* updating basic contact information

### Patient Record (Clinic System)

Managed exclusively by clinic staff and used for:

* patient identification
* dental charts
* treatment history
* prescriptions
* official appointment records

---

# Booking Workflow

### Patient Portal

Patients can:

* create an account
* log in
* request an appointment

These requests are sent to the clinic for confirmation.

### Clinic Staff

Staff members:

* review appointment requests
* identify or create the correct patient record
* attach the appointment to that record

This ensures that **only verified clinic records are used for medical data**.

---

# Benefits of This Approach

## Improved Data Integrity

Only clinic staff can create or modify official patient records.

## Reduced Duplicate Records

Patient records are no longer automatically created during account registration.

## Enhanced Privacy Protection

Portal accounts cannot directly access sensitive medical records.

## Simpler System Architecture

The system avoids complex identity-matching and verification logic.

---

# Conclusion

Separating patient accounts from clinic patient records allows the system to remain **secure, manageable, and aligned with real clinic workflows**.

The patient portal acts as a **communication and appointment interface**, while the clinic system remains the authoritative source for **medical records and patient management**.
