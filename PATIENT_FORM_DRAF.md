# Crash-Safe Patient Form Draft Recovery

## Overview

To prevent data loss during browser crashes, refreshes, or power
interruptions, the patient form will implement a hybrid draft recovery
system.

This system automatically saves form progress so staff can restore
unfinished work when reopening the patient form modal.

------------------------------------------------------------------------

# System Design

## Local Draft Storage

Saved in the browser using localStorage.

Benefits: - Instant save - Protects against browser crashes - Protects
against refresh or tab close

## Server Draft Sync

Every 15 seconds the client syncs the draft to the server.

Benefits: - Recovery across devices - Backup if local storage is lost

------------------------------------------------------------------------

# Database Structure

Table: patient_form_drafts

Columns: - id - user_id - patient_id - mode (create/edit) - step -
payload_json - updated_at - expires_at

Unique key context: (user_id, mode, patient_id)

For create mode, patient_id is treated as 0.

------------------------------------------------------------------------

# Draft Lifecycle

## Draft Creation

When the modal opens: 1. Check localStorage 2. Check server draft 3. Use
newest updated_at 4. Ask user whether to restore or discard

------------------------------------------------------------------------

## Draft Saving

Local save triggers: - input change - dental chart change - step
navigation - beforeunload

localStorage key format:
clinic:patient-form-draft:{userId}:{mode}:{patientId\|new}

------------------------------------------------------------------------

## Server Sync

If the form is dirty and modal is open, sync every 15 seconds.

Server method: saveDraftFromClient(array \$payload)

------------------------------------------------------------------------

# Payload Example

{ "currentStep": 2, "basicInfo": {}, "healthHistory": {}, "dentalChart":
{}, "treatmentRecord": {}, "updatedAt": "" }

------------------------------------------------------------------------

# Image Handling

Drafts do NOT store images.

Images upload only during final save.

------------------------------------------------------------------------

# Restore Workflow

When modal opens and a draft exists:

Prompt: Restore Draft Discard Draft

Restore loads: - step - basic info - health history - dental chart -
treatment record

Discard deletes both server and local drafts.

------------------------------------------------------------------------

# Draft Clearing

After successful record save: - delete server draft - remove local
draft - reset dirty state

------------------------------------------------------------------------

# Security

-   drafts scoped to user
-   expire after 7 days
-   cleared after save
-   cleared after discard

------------------------------------------------------------------------

# Testing

Unit tests: - upsert draft - fetch draft - discard draft - expiration
cleanup

Manual tests: - browser refresh recovery - step restoration - discard
behavior - create/edit isolation

------------------------------------------------------------------------

# Conclusion

The crash-safe draft system protects staff work when entering long
patient records and improves overall workflow reliability.
