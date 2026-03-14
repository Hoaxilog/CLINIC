# Appointment Calendar Design Fixes

## Goal

Make each calendar slot look longer, improve text readability, and reduce the cramped look of appointment cards.

## Main Problems

* Day columns are too narrow.
* Appointment cards use too much inner padding.
* Text wraps or truncates too early.
* Overlay spacing makes each card look smaller than it should.
* Blocked slots feel heavier than regular appointments.

## Best Fixes

### 1. Make each day column wider

Replace this:

```html
grid-cols-[88px_repeat(7,minmax(170px,1fr))] xl:grid-cols-[96px_repeat(7,minmax(180px,1fr))] min-w-[1278px] xl:min-w-[1356px]
```

With this:

```html
grid-cols-[92px_repeat(7,minmax(190px,1fr))] xl:grid-cols-[100px_repeat(7,minmax(210px,1fr))] min-w-[1422px] xl:min-w-[1570px]
```

Use this replacement in:

* calendar header grid
* main calendar body grid
* calendar overlay grid

## 2. Make the appointment card cleaner and more readable

Replace the single appointment card with this:

```html
<div wire:key="appointment-{{ $firstAppt->id }}"
    @click="modalOpen = true"
    wire:click="viewAppointment({{ $firstAppt->id }})"
    class="w-full h-full rounded-xl px-3 py-2 border border-blue-300 border-l-[4px] bg-white shadow-sm overflow-hidden pointer-events-auto cursor-pointer flex flex-col hover:shadow-md transition">

    <p class="text-[13px] lg:text-sm font-bold text-slate-900 leading-tight truncate">
        {{ $firstAppt->last_name }}, {{ $firstAppt->first_name }}
    </p>

    <p class="mt-1 text-[12px] font-medium text-slate-600 leading-tight truncate">
        {{ $firstAppt->service_name }}
    </p>

    <p class="mt-1 text-[12px] font-semibold text-blue-600 whitespace-nowrap">
        {{ $firstAppt->start_time }} - {{ $firstAppt->end_time }}
    </p>

    <p class="mt-1 text-[11px] font-semibold uppercase tracking-wide truncate
        @if ($firstAppt->status == 'Ongoing') text-amber-600
        @elseif($firstAppt->status == 'Scheduled') text-blue-600
        @elseif($firstAppt->status == 'Cancelled') text-red-600
        @elseif($firstAppt->status == 'Waiting') text-orange-600
        @elseif($firstAppt->status == 'Completed') text-green-600
        @else text-gray-600 @endif">
        {{ $firstAppt->status === 'Waiting' ? 'Ready' : $firstAppt->status }}
    </p>
</div>
```

## 3. Reduce wasted side space in the overlay

Replace this:

```html
class="absolute inset-x-0 px-1"
```

With this:

```html
class="absolute inset-x-0 px-0.5"
```

This lets the event card use more of the available day width.

## 4. Make blocked slots lighter and cleaner

Replace this blocked state:

```html
bg-red-100 text-red-800 cursor-pointer hover:bg-red-200
```

With this:

```html
bg-red-50 text-red-800 cursor-pointer hover:bg-red-100
```

Replace the blocked slot inner content with this:

```html
<div class="h-full w-full px-3 py-2.5 flex flex-col">
    <p class="text-[11px] md:text-xs font-bold uppercase tracking-[0.12em] text-red-700">Blocked</p>
    @if (!empty($blockedSlot->reason))
        <p class="hidden md:block mt-1 text-[11px] text-red-600 truncate">
            {{ $blockedSlot->reason }}
        </p>
    @endif
</div>
```

## 5. Give the calendar more usable width

Replace this outer wrapper:

```html
<div class="w-full max-w-9xl mx-auto px-2 py-6 lg:px-8 overflow-x-auto bg-white mt-6">
```

With this:

```html
<div class="w-full max-w-[100rem] mx-auto px-1 sm:px-2 lg:px-4 py-6 overflow-x-auto bg-white mt-6">
```

## Priority Order

If you want the biggest visual improvement first, do these in order:

1. Increase the grid width.
2. Replace the event card design.
3. Reduce overlay side spacing.
4. Lighten the blocked slot style.
5. Reduce outer wrapper padding.

## Expected Result

After these changes:

* each day column will look longer
* the event cards will feel less cramped
* patient names will stay readable longer
* service, time, and status will look cleaner
* blocked slots will match the calendar better visually

## Main Reason the Current Design Feels Broken

The issue is more about **horizontal compression** than height. The cards are losing usable text space because of:

* tight column width
* extra padding
* extra side spacing
* text hierarchy that is too large for the available width

So the correct fix is:
**more usable width + tighter card layout**, not just taller slots.
