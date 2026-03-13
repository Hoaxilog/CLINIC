# Lobby Flow UI Performance Optimization Plan

## Goal

Improve the **speed and responsiveness** of the Lobby Flow UI so actions such as opening cards, calling patients, or closing modals feel **instant (<200ms)** instead of the current **~1.5 second delay**.

This document focuses on **frontend interaction speed**, **Livewire optimization**, and **UI rendering improvements** without changing the system’s core logic.

---

# Current Problem

Users experience delays (~1.5s) when performing simple actions such as:

* Closing modals
* Opening appointment details
* Clicking patient cards
* Calling the next patient
* Updating queue states

This delay usually happens because:

* The entire component re-renders
* The full queue list refreshes
* The UI waits for a server response before updating
* Livewire hydration/dehydration cycles are heavy

---

# Target UX

The system should feel **instant and reactive**.

| Action                  | Target Speed |
| ----------------------- | ------------ |
| Open patient details    | <100ms       |
| Close modal             | Instant      |
| Call next patient       | <200ms       |
| Move patient to session | <200ms       |
| Update queue visually   | Instant      |
| Background refresh      | Invisible    |

Users should **never feel the system waiting**.

---

# Core Optimization Strategy

We improve performance by shifting interactions from **server-first** to **client-first**.

Instead of:

```
Click -> Server -> Render -> UI Update
```

We move to:

```
Click -> UI Update -> Server Sync
```

This removes the perceived delay.

---

# Phase 1 — Stop Full Component Re-Renders

The biggest slowdown usually happens when the entire component refreshes.

### Problem

When a single action happens, the system refreshes:

* Today Schedule
* Ready in Lobby
* In Session

Even if only one patient changed.

### Solution

Split the lobby into **independent components**.

```
LobbyPage
│
├── TodayScheduleComponent
├── ReadyLobbyComponent
└── InSessionComponent
```

Each section updates independently.

Benefits:

* Smaller DOM updates
* Faster interaction
* Less Livewire hydration cost

---

# Phase 2 — Instant Modal Behavior

### Problem

Closing modals currently waits for Livewire response.

### Solution

Handle modal open/close **entirely in JavaScript**.

Example logic:

```
UI Click
→ Modal closes immediately
→ Background request updates server
```

This ensures users never wait to close a modal.

Recommended approach:

* Alpine.js
* Lightweight JS event handling

---

# Phase 3 — Lazy Data Refresh

Instead of refreshing the entire board after every action, use **targeted updates**.

Example:

Calling next patient should only update:

* Ready in Lobby
* In Session

NOT Today Schedule.

---

# Phase 4 — Lightweight Patient Cards

Each patient card should be treated as a **small reactive element** instead of forcing list refresh.

Current issue:

```
One card change
→ Full list re-render
```

Improved behavior:

```
One card change
→ Only that card updates
```

This significantly reduces DOM work.

---

# Phase 5 — Reduce Livewire Payload Size

Large components increase response time.

Optimize by:

* Removing unnecessary data from components
* Avoid passing entire collections repeatedly
* Use IDs instead of full objects when possible

Example:

Instead of sending full appointment objects repeatedly, send minimal state data.

---

# Phase 6 — Optimized Polling Strategy

Polling too frequently causes UI lag.

### Current Problem

Polling may compete with user interactions.

### Recommended Strategy

| Section        | Polling    |
| -------------- | ---------- |
| Today Schedule | 60 seconds |
| Ready in Lobby | 10 seconds |
| In Session     | 5 seconds  |

Alternatively, move to **event-based updates** instead of polling.

---

# Phase 7 — Client-Side UI Feedback

Even if the server takes time, the UI should react immediately.

Example:

### Calling Next Patient

Immediate UI change:

```
Ready in Lobby
→ Patient removed

In Session
→ Patient appears
```

Server confirms afterward.

Users perceive the action as instant.

---

# Phase 8 — Reduce DOM Complexity

Heavy DOM trees slow rendering.

Optimize by:

* Removing deeply nested elements
* Avoiding unnecessary wrappers
* Keeping cards simple

Example:

Bad:

```
div
 └ div
   └ div
     └ card
```

Better:

```
card
```

---

# Phase 9 — Skeleton Loading

Instead of freezing the UI during updates, use **skeleton loaders**.

Example:

```
Loading patient card...
```

This improves perceived speed.

---

# Phase 10 — Efficient Event Handling

Use event-driven updates instead of full refresh cycles.

Example events:

```
PatientArrived
PatientCalled
SessionStarted
SessionCompleted
```

Each event updates only the necessary UI section.

---

# UI Interaction Performance Targets

| Interaction       | Ideal Behavior          |
| ----------------- | ----------------------- |
| Close modal       | Instant                 |
| Open patient card | <100ms                  |
| Call next patient | <200ms                  |
| Move to session   | <200ms                  |
| Update queue      | Immediate visual change |

---

# Visual Feedback Improvements

Fast UI also requires clear feedback.

Add:

### Loading States

Buttons show activity while server processes.

Example:

```
Calling...
```

### Disabled Buttons

Prevent duplicate actions during processing.

---

# Optional Advanced Optimization

These improvements are optional but highly recommended for larger systems.

### WebSockets

Replace polling with real-time updates.

Benefits:

* Instant queue changes
* Lower server load
* True live dashboard

---

### Virtualized Lists

If patient queues grow large, render only visible items.

This prevents heavy DOM usage.

---

# Example Fast Interaction Flow

### Current Flow

```
Click Call Next
↓
Wait for server
↓
UI refresh
↓
Patient moves
```

### Optimized Flow

```
Click Call Next
↓
UI moves patient instantly
↓
Server sync happens
↓
Background confirmation
```

---

# Expected Results

After implementing these improvements:

* Modal close time becomes **instant**
* Queue actions drop from **1.5s → <200ms**
* UI feels real-time
* Livewire load decreases
* System handles higher patient volume smoothly

---

# Implementation Summary

| Area                | Change                                   |
| ------------------- | ---------------------------------------- |
| Component Structure | Split into smaller Livewire components   |
| Modal Behavior      | Move open/close to JavaScript            |
| Data Updates        | Targeted refresh instead of full refresh |
| Polling             | Reduce frequency                         |
| UI Updates          | Client-first rendering                   |
| DOM                 | Simplify structure                       |

---

# Final Objective

Transform the lobby flow into a **high-performance operational board** where actions feel immediate and the UI always stays responsive, even during heavy clinic activity.
