# Appointment Calendar Performance Optimization Plan
Laravel + Livewire

## Overview
This document defines the performance improvement plan for the Appointment Calendar system.
The goal is to make calendar interactions feel instant and responsive while maintaining the current architecture.

Two phases are defined:
- Phase 1 — Optimize the existing Livewire implementation
- Phase 2 — Introduce hybrid architecture if needed

Phase 1 should solve most performance problems without a full rewrite.

---

## Performance Targets

| Interaction | Target |
|---|---|
| Open slot modal | <150ms perceived |
| Open appointment details | <150ms |
| Close modal | <100ms |
| Save appointment | <500ms |
| Status update | <300ms |
| Week navigation | <400ms |

---

## Current Performance Problems

### 1. Full Calendar Re-render
The calendar grid re-renders whenever:
- modal opens
- modal closes
- appointment is saved
- appointment status changes
- polling refresh runs

The calendar contains 100+ DOM elements, which makes full rerenders expensive.

### 2. Heavy `loadAppointments()` Method
The current method:
- joins patients and services
- parses Carbon dates repeatedly
- calculates appointment durations
- groups appointments by time
- rebuilds slot occupancy counts

This runs frequently and creates unnecessary server work.

### 3. Polling Conflicts
Current implementation:

`wire:poll.15s.visible="loadAppointments"`

Polling can run while users are interacting, which causes UI delays.

### 4. Large Livewire Component State
The component stores many public properties:
- appointments
- occupied slot counts
- week dates
- patient search results
- modal state

Large state increases Livewire hydration cost.

### 5. Confirmed Server Roundtrip Delay on Modal Close
Browser timing shows that even closing the modal takes around **1.3 seconds waiting for server response**.

This confirms the delay is mainly caused by:
- Livewire request roundtrip
- component hydration/dehydration
- full parent component re-render

This is important because closing the modal is a UI-only action and should not depend on a heavy server lifecycle.

---

# Phase 1 — Optimize Current Livewire Implementation

## Step 1 — Add `wire:key` to Loops

### Day Columns

```blade
@foreach ($weekDates as $date)
    <div wire:key="calendar-day-{{ $date->format('Y-m-d') }}">
```

### Time Slots

```blade
@foreach ($timeSlots as $time)
    <div wire:key="calendar-slot-{{ $time }}">
```

### Appointment Cards

```blade
<div wire:key="appointment-{{ $appointment->id }}">
```

This allows Livewire to patch only changed elements.

---

## Step 2 — Split `loadAppointments()`

### Refresh Appointment Data

```php
public function refreshAppointments()
{
    $start = $this->weekDates[0]->startOfDay();
    $end = $this->weekDates[6]->endOfDay();

    $this->appointments = DB::table('appointments')
        ->join('patients', 'appointments.patient_id', '=', 'patients.id')
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->whereBetween('appointment_date', [$start, $end])
        ->select(
            'appointments.id',
            'appointments.appointment_date',
            'appointments.status',
            'patients.first_name',
            'patients.last_name',
            'services.service_name'
        )
        ->get();
}
```

### Refresh Slot Occupancy

```php
public function refreshSlotCounts()
{
    $this->occupiedAppointments = DB::table('appointments')
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->whereIn('appointments.status', self::APPROVED_SLOT_STATUSES)
        ->get();

    $this->rebuildOccupiedSlotCounts();
}
```

Only call slot-count rebuilding when truly necessary:
- week changes
- appointment created
- appointment deleted
- service/time changes that affect occupancy

Do not rebuild slot counts for purely visual actions.

---

## Step 3 — Reduce Polling

Replace:

`wire:poll.15s.visible="loadAppointments"`

With:

`wire:poll.30s="refreshAppointments"`

Polling should never interrupt modal interactions.

---

## Step 4 — Optimize Patient Search

Replace:

`wire:model.live.debounce.300ms="searchQuery"`

With:

`wire:model.debounce.500ms="searchQuery"`

or:

`wire:model.lazy="searchQuery"`

This reduces server chatter while typing.

---

## Step 5 — Prevent Calendar Re-render When Modal Opens

Wrap the calendar grid with:

```blade
<div wire:ignore.self>
```

This prevents modal state changes from forcing the entire calendar grid to redraw.

---

## Step 6 — Add Button Loading States

```blade
<button
    wire:click="approveAppointment({{ $pending->id }})"
    wire:loading.attr="disabled"
    class="bg-green-600 text-white px-3 py-2">
    Approve
</button>
```

This improves perceived responsiveness.

---

## Step 7 — Add Global Loading Indicator

```blade
<div wire:loading.flex class="fixed inset-0 bg-black/10 z-50 items-center justify-center">
    <div class="bg-white px-6 py-3 rounded shadow">
        Processing...
    </div>
</div>
```

This prevents the UI from feeling frozen.

---

## Step 8 — Reduce Carbon Parsing

Instead of repeatedly parsing:

`Carbon::parse($appointment->appointment_date)`

Parse once and reuse:

```php
$appointment->start = Carbon::parse($appointment->appointment_date);
```

---

## Step 9 — Database Indexing

Recommended indexes:

- `appointments(appointment_date)`
- `appointments(status, appointment_date)`
- `appointments(dentist_id, appointment_date)`
- `appointments(patient_id, appointment_date)`

These improve weekly queries and status updates.

---

## Step 10 — Reduce Modal State Size

The modal should only store:
- appointmentId
- selectedDate
- selectedTime
- selectedService
- patient fields

Do not keep full appointment collections inside modal state.

---

## Step 11 — Make Modal Open/Close Client-Side First

This is now a **high-priority fix** because timing confirmed that modal close is spending ~1.3s waiting for the server.

### Problem
Current close button uses:

```blade
wire:click="closeAppointmentModal"
```

And the method does:

```php
public function closeAppointmentModal()
{
    $this->showAppointmentModal = false;
    $this->resetForm();
}
```

That means closing the modal still:
1. sends a Livewire request
2. hydrates the whole component
3. resets form state on the server
4. returns a rerendered response

For a simple UI close action, this is too expensive.

### Recommended Fix
Use **Alpine** for modal visibility so opening and closing happen instantly in the browser.

Example concept:

```blade
<div x-data="{ open: @entangle('showAppointmentModal').defer }">
    <div x-show="open" x-transition>
        ...
        <button type="button" @click="open = false">×</button>
    </div>
</div>
```

### Important Rule
- **UI visibility** should be client-side
- **data loading / saving / status updates** can stay in Livewire

This gives instant interaction while preserving current server logic.

---

## Step 12 — Do Not Reset Form on Every Close

Move expensive cleanup out of the visual close path.

### Current pattern
- click close
- server request
- reset form
- rerender
- modal disappears

### Better pattern
- click close
- modal hides instantly in browser
- form state resets only when opening a new appointment flow, after save, or when explicitly cleaning up

Suggested idea:

```php
public function prepareNewAppointment($date, $time)
{
    $this->resetForm();
    $this->selectedDate = $date;
    $this->selectedTime = $time;
    $this->showAppointmentModal = true;
}
```

This keeps close interactions lightweight.

---

## Step 13 — Optional Next Step: Extract Modal into Child Component

If modal actions still feel heavy after client-side visibility changes, extract the modal into its own Livewire component.

Suggested structure:

```text
AppointmentCalendar
└── AppointmentModal
```

This reduces parent component rerendering and is a strong intermediate step before a full hybrid rewrite.

---

## Phase 1 Success Criteria

Phase 1 is complete when:
- modal opens instantly
- modal closes instantly
- calendar does not fully rerender on modal open/close
- status updates feel immediate
- week navigation is smoother
- polling does not interrupt interactions

---

# Phase 2 — Hybrid Calendar Architecture (Future)

If performance still needs improvement, move to hybrid architecture.

## Goals
- client-rendered calendar grid
- JSON weekly endpoint
- async appointment updates
- optimistic UI updates

## Example API

`GET /api/appointments`

Example response:

```json
{
  "data": [
    {
      "id": 123,
      "patient_id": 88,
      "patient_name": "Doe, Jane",
      "service_name": "Cleaning",
      "start": "2026-03-12T10:00:00",
      "end": "2026-03-12T10:30:00",
      "status": "Scheduled"
    }
  ]
}
```

---

# Final Summary

Phase 1 focuses on optimizing the current Livewire implementation with minimal changes.

Key additions from real testing:
- the biggest confirmed bottleneck is server roundtrip for modal close
- modal visibility should be moved client-side
- reset and cleanup should not block close interactions

Benefits:
- faster interactions
- reduced rendering cost
- improved UX
- no full system rewrite required yet

Phase 2 introduces hybrid architecture only if additional improvements are still needed.
