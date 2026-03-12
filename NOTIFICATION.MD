# Notification System Design Guide
Dental Clinic Management System (Laravel + Filament)

This document explains how the **notification panel** should work inside the admin system.
It is based on the Filament notification drawer UI and includes the expected **right-side slide-over behavior**.

The goal is to provide **real-time alerts and updates** to staff and administrators.

---

# Purpose of Notifications

Notifications help staff immediately see **important system events** without navigating to other modules.

Typical events include:

• New appointment request  
• Appointment approved or cancelled  
• Walk-in patient registered  
• Patient record updated  
• Lobby flow status changes  
• System alerts

The notification panel should act as a **quick activity inbox**.

---

# Notification Panel Layout

The panel appears when clicking the **bell icon** in the top navigation bar.

It should behave like a **right-side slide-over drawer**.

Visual structure:

```text
┌───────────────────────────────┐
│ Notifications (7)             │
│ Mark all as read | Clear      │
├───────────────────────────────┤
│ New Appointment Request       │
│ 5 minutes ago                 │
│ Juan Cruz requested Cleaning  │
│ View                          │
├───────────────────────────────┤
│ Appointment Approved          │
│ 10 minutes ago                │
│ Maria Santos appointment ok   │
│ View                          │
├───────────────────────────────┤
│ Walk-in Patient Registered    │
│ 20 minutes ago                │
│ Jerome Bernacer added to flow │
│ View                          │
└───────────────────────────────┘
```

---

# Drawer Behavior

The notification panel should:

• open from the **right side**  
• slide **left into view**  
• cover full height of the viewport  
• show a dark overlay behind it  
• close when clicking the close button  
• close when clicking the overlay  
• optionally close when pressing `Esc`

This is the same UI pattern used by **modern SaaS admin panels**.

---

# Slide-Over Motion

Closed state:

• drawer is positioned off-screen on the right  
• overlay is hidden  

Open state:

• drawer translates into view from right to left  
• overlay fades in behind it  

Recommended motion concept:

```text
Closed  → translateX(100%)
Open    → translateX(0)
Overlay → opacity 0 to opacity 1
```

Recommended transition:

• `transform 0.3s ease-in-out`  
• `opacity 0.3s ease`

---

# Suggested Drawer Dimensions

Recommended size:

• width: **380px to 420px**  
• height: **100vh**  
• fixed to the top-right corner  

This keeps the panel readable without covering the entire page.

---

# Drawer Layout Structure

The drawer should contain:

## 1. Header
Displays:

• Title: **Notifications**  
• Unread count badge  
• Mark all as read  
• Clear  
• Close button

Example:

```text
Notifications (7)
Mark all as read | Clear | X
```

## 2. Scrollable Body
The list of notifications should scroll vertically if there are many items.

## 3. Optional Footer
Optional footer actions:

• View All Notifications  
• Notification Settings

---

# Notification Item Structure

Each notification item should contain:

| Element | Description |
|------|------|
| Icon | Represents event type |
| Title | Short description |
| Timestamp | When the event occurred |
| Message | Extra context |
| Action | Link to related module |

Example:

```text
New Appointment Request
5 minutes ago
Juan Cruz requested Cleaning
View
```

---

# Suggested Notification Types

## Appointment Notifications

Triggered when:

• New appointment request submitted  
• Appointment approved  
• Appointment cancelled  

Example:

```text
New Appointment Request
Juan Cruz requested a Cleaning appointment
```

---

## Lobby Flow Notifications

Triggered when:

• Walk-in patient registered
• Patient admitted to chair
• Treatment completed

Example:

```text
Patient Admitted
Maria Santos admitted to treatment chair
```

---

## Patient Record Notifications

Triggered when:

• New patient registered
• Patient record updated

Example:

```text
New Patient Registered
Jerome Bernacer added to patient records
```

---

# Notification Actions

Each notification should include a **View button** that opens the related module.

Examples:

| Notification | Opens |
|------|------|
| Appointment request | Appointment Requests page |
| Appointment approved | Appointment details |
| Walk-in registered | Lobby Flow |
| New patient | Patient Records |

---

# Notification Behavior

## Unread Notifications

Unread notifications should be:

• Highlighted
• Included in the **notification count badge**

Example badge:

```text
🔔 7
```

---

## Mark as Read

Users should be able to:

• Mark a single notification as read
• Mark all notifications as read

When marked as read:

• Badge count decreases
• Highlight disappears

---

## Clear Notifications

The **Clear button** removes notifications from the list.

This does **not delete system logs**, only the notification view.

---

# Notification Categories (Optional)

If the system grows, notifications can be grouped by type:

```text
Appointments
Patients
Lobby Flow
System
```

This prevents the list from becoming cluttered.

---

# Real-Time Updates

Notifications should update automatically when:

• A new appointment request is submitted
• A walk-in patient registers
• A patient status changes

This can be implemented with:

• polling
• websockets
• event broadcasting

---

# UI Guidelines

To keep the notification panel clean:

✔ Limit list height and enable scrolling  
✔ Show **latest notifications first**  
✔ Keep titles short  
✔ Use icons for visual clarity  
✔ Avoid long text messages  
✔ Keep header visible while scrolling  
✔ Use a soft shadow for the drawer  
✔ Use a dark translucent overlay behind the drawer  

---

# Example Notification Flow

```text
Patient submits appointment request
        ↓
System creates notification
        ↓
Bell icon shows unread count
        ↓
Admin clicks notification bell
        ↓
Drawer slides in from the right
        ↓
Admin reads notification and clicks View
        ↓
Admin is taken to the related module
```

---

# Example Notifications for This System

• New Appointment Request  
• Appointment Approved  
• Appointment Cancelled  
• Walk-in Patient Registered  
• Patient Admitted to Chair  
• Treatment Completed  
• New Patient Registered  

---

# Result

A well-designed slide-over notification system allows staff to:

✔ Respond quickly to new appointments  
✔ Monitor clinic activity  
✔ Navigate directly to relevant modules  
✔ Stay informed without switching pages  
✔ Use a modern notification drawer experience similar to Filament and SaaS dashboards
