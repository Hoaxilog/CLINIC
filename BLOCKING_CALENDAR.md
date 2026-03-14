# Calendar Slot Blocking – Feature Wireframe & UX Plan

## Goal

Allow the admin or front desk to **block specific calendar slots** so appointments cannot be booked during unavailable times (dentist leave, lunch break, clinic maintenance, etc.).

The feature should:

* Be **fast and responsive**
* Avoid **full calendar re-render**
* Clearly show **blocked vs available slots**
* Allow **easy unblock/edit**

---

# 1. Calendar Layout

## Header Controls

```
 ---------------------------------------------------------
|  Calendar Header                                        |
|                                                         |
|  [ < ]  March 2026  [ > ]                               |
|                                                         |
|  [ + New Appointment ]   [ Block Time ]                 |
 ---------------------------------------------------------
```

### Description

* **Previous / Next Month** → navigate calendar
* **New Appointment** → create appointment
* **Block Time** → open block slot modal

---

# 2. Calendar Grid

```
| Time      | Chair 1        | Chair 2        | Chair 3 |
|-----------|---------------|---------------|-----------|
| 9:00 AM   | Available     | Appointment   | Available |
| 9:30 AM   | Available     | Appointment   | Available |
| 10:00 AM  | BLOCKED       | Available     | Available |
| 10:30 AM  | BLOCKED       | Available     | Available |
| 11:00 AM  | Available     | Available     | Available |
```

### Slot Types

| Type        | Color                  | Meaning              |
| ----------- | ---------------------- | -------------------- |
| Available   | `#ffffff`              | Can book appointment |
| Appointment | `#3b82f6`              | Already scheduled    |
| Blocked     | `#ef4444` or `#6b7280` | Cannot book          |

---

# 3. Block Slot Modal

When the user clicks **Block Time**.

```
 -----------------------------
| Block Time Slot             |
 -----------------------------

 Date
 [ March 20, 2026 ]

 Start Time
 [ 10:00 AM ]

 End Time
 [ 12:00 PM ]

 Chair
 ( ) All Chairs
 ( ) Chair 1
 ( ) Chair 2
 ( ) Chair 3

 Reason
 [ Dentist Meeting ]

 [ Cancel ]   [ Block Slot ]
```

### Expected Behavior

* Blocks every slot between **start and end time**
* Can block **all chairs or specific chair**

---

# 4. Calendar Result After Blocking

```
| Time      | Chair 1 | Chair 2 | Chair 3 |
|-----------|--------|--------|--------|
| 10:00 AM  | BLOCKED | BLOCKED | BLOCKED |
| 10:30 AM  | BLOCKED | BLOCKED | BLOCKED |
| 11:00 AM  | BLOCKED | BLOCKED | BLOCKED |
```

Blocked slots should visually appear as:

```
████████████████
   BLOCKED
Dentist Meeting
```

---

# 5. Blocked Slot Details

Clicking a blocked slot opens details.

```
 ----------------------------
| Blocked Slot               |
 ----------------------------

 Reason: Dentist Meeting
 Date: March 20
 Time: 10:00 – 12:00
 Chair: All Chairs

 [ Edit ]   [ Unblock ]
```

### Actions

**Edit**

* Modify time
* Modify reason

**Unblock**

* Remove block
* Slots become available again

---

# 6. Performance Optimization

To prevent **slow UI interaction (like the 1.5s delay)**.

### Avoid

* Full calendar refresh
* Re-rendering the entire grid
* Heavy server calls for UI actions

### Recommended Flow

```
User clicks "Block Slot"
        ↓
UI instantly marks slot as blocked
        ↓
Async request saves block to database
        ↓
If success → keep UI
If fail → revert change
```

This technique is called **Optimistic UI**.

---

# 7. Database Structure

Create a table:

```
blocked_slots
--------------
id
date
start_time
end_time
chair_id (nullable)
reason
created_by
created_at
```

### Example Record

```
date: 2026-03-20
start_time: 10:00
end_time: 12:00
chair_id: null
reason: Dentist Meeting
```

`chair_id = null` means **block all chairs**.

---

# 8. Advanced Feature (Optional)

## Recurring Block

Example:

```
Block Every Friday
9:00 AM – 11:00 AM
```

Use cases:

* Weekly dentist meeting
* Lunch break
* Cleaning time

---

# 9. Drag Selection (Future UX Improvement)

Instead of using only a button.

User can **drag across time slots**.

```
User Drag
10:00 → 11:30
```

Then confirmation modal appears.

```
Block these slots?
[ Cancel ] [ Confirm ]
```

This interaction is similar to **Google Calendar**.

---

# 10. Recommended Block Types

For a dental system:

| Type               | Example            |
| ------------------ | ------------------ |
| Dentist Leave      | Doctor unavailable |
| Clinic Maintenance | Equipment cleaning |
| Emergency Closure  | Unexpected closure |

Each type can have **different color indicators**.

Example:

```
Dentist Leave → Red
Maintenance → Yellow
Closure → Dark Gray
```

---

# Final Summary

The slot blocking system should:

* Visually display **blocked time slots**
* Allow **easy block / unblock**
* Support **chair-specific blocking**
* Avoid **slow calendar re-render**
* Provide **clear UI feedback**

This keeps the **appointment system reliable and fast for front desk staff**.
