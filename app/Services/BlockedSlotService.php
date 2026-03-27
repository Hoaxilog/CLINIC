<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BlockedSlotService
{
    public const SLOT_CAPACITY = 2;

    private ?bool $tableExists = null;

    public function isEnabled(): bool
    {
        if ($this->tableExists === null) {
            $this->tableExists = Schema::hasTable('blocked_slots');
        }

        return $this->tableExists;
    }

    /**
     * Quick-insert a single 1-hour blocked slot from the calendar click.
     * Returns ['ok' => bool, 'message' => string]
     */
    public function quickBlock(string $date, string $time, array $occupiedSlotCounts, array $blockedSlotMap, ?int $chairId = null): array
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $slotStart = Carbon::parse($date . ' ' . $normalizedTime)->seconds(0);
        $slotEnd = $slotStart->copy()->addHour();
        $slotKey = $slotStart->format('Y-m-d H:i');
        $normalizedChairId = in_array($chairId, [1, 2], true) ? $chairId : null;

        if ($normalizedChairId === null && ($blockedSlotMap[$slotKey] ?? false) === true) {
            return ['ok' => false, 'message' => 'That slot is already blocked.'];
        }

        if ($normalizedChairId === null && ($occupiedSlotCounts[$slotKey] ?? 0) > 0) {
            return ['ok' => false, 'error' => true, 'message' => 'Cannot block a slot that already has appointments.'];
        }

        $hasOverlap = DB::table('blocked_slots')
            ->whereDate('date', $slotStart->toDateString())
            ->when(
                $normalizedChairId !== null,
                fn ($query) => $query->where(function ($nested) use ($normalizedChairId) {
                    $nested->whereNull('chair_id')
                        ->orWhere('chair_id', $normalizedChairId);
                })
            )
            ->where('start_time', '<', $slotEnd->format('H:i:s'))
            ->where('end_time', '>', $slotStart->format('H:i:s'))
            ->exists();

        if ($hasOverlap) {
            return ['ok' => false, 'message' => 'That slot is already blocked.'];
        }

        DB::table('blocked_slots')->insert([
            'date' => $slotStart->toDateString(),
            'start_time' => $slotStart->format('H:i:s'),
            'end_time' => $slotEnd->format('H:i:s'),
            'chair_id' => $normalizedChairId,
            'reason' => null,
            'created_by' => $this->resolveBlockingActor(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['ok' => true, 'message' => 'Time slot blocked. Select another slot to continue blocking.'];
    }

    /**
     * Save (create or update) a blocked slot. Returns ['ok' => bool, 'message' => string]
     */
    public function save(?int $blockingSlotId, string $blockDate, string $blockStartTime, string $blockEndTime, ?string $blockReason, ?int $chairId = null): array
    {
        $start = Carbon::parse($blockDate . ' ' . $blockStartTime);
        $end = Carbon::parse($blockDate . ' ' . $blockEndTime);
        $normalizedChairId = in_array($chairId, [1, 2], true) ? $chairId : null;

        $hasOverlap = DB::table('blocked_slots')
            ->whereDate('date', $blockDate)
            ->when($blockingSlotId, fn($q) => $q->where('id', '!=', $blockingSlotId))
            ->when(
                $normalizedChairId !== null,
                fn ($query) => $query->where(function ($nested) use ($normalizedChairId) {
                    $nested->whereNull('chair_id')
                        ->orWhere('chair_id', $normalizedChairId);
                })
            )
            ->where('start_time', '<', $end->format('H:i:s'))
            ->where('end_time', '>', $start->format('H:i:s'))
            ->exists();

        if ($hasOverlap) {
            return ['ok' => false, 'field' => 'blockStartTime', 'message' => 'This range overlaps another blocked slot.'];
        }

        $payload = [
            'date' => $blockDate,
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'chair_id' => $normalizedChairId,
            'reason' => trim($blockReason ?? '') !== '' ? trim($blockReason) : null,
            'updated_at' => now(),
        ];

        if ($blockingSlotId) {
            DB::table('blocked_slots')->where('id', $blockingSlotId)->update($payload);
        }
        else {
            $payload['created_by'] = $this->resolveBlockingActor();
            $payload['created_at'] = now();
            DB::table('blocked_slots')->insert($payload);
        }

        return ['ok' => true, 'message' => $blockingSlotId ? 'Blocked slot updated.' : 'Time slot blocked.'];
    }

    /**
     * Delete a blocked slot by ID.
     */
    public function delete(int $blockedSlotId): void
    {
        DB::table('blocked_slots')->where('id', $blockedSlotId)->delete();
    }

    /**
     * Load a blocked slot by ID for editing.
     */
    public function find(int $blockedSlotId): ?object
    {
        return DB::table('blocked_slots')->where('id', $blockedSlotId)->first();
    }

    /**
     * Check if a proposed time range overlaps any blocked slot.
     */
    public function hasConflict(Carbon $proposedStart, Carbon $proposedEnd): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return $this->blockedCapacityForRange($proposedStart, $proposedEnd) >= self::SLOT_CAPACITY;
    }

    /**
     * Load all blocked slots for a date range.
     */
    public function forWeek(Carbon $startOfWeek, Carbon $endOfWeek): \Illuminate\Support\Collection
    {
        if (!$this->isEnabled()) {
            return collect();
        }

        return DB::table('blocked_slots')
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    public function blockedCapacityForRange(Carbon $proposedStart, Carbon $proposedEnd, int $slotCapacity = self::SLOT_CAPACITY): int
    {
        if (! $this->isEnabled()) {
            return 0;
        }

        $blockedSlots = DB::table('blocked_slots')
            ->whereDate('date', $proposedStart->toDateString())
            ->where('start_time', '<', $proposedEnd->format('H:i:s'))
            ->where('end_time', '>', $proposedStart->format('H:i:s'))
            ->get();

        return $this->blockedCapacityForRangeFromCollection($proposedStart, $proposedEnd, $blockedSlots, $slotCapacity);
    }

    public function blockedCapacityForRangeFromCollection(Carbon $proposedStart, Carbon $proposedEnd, iterable $blockedSlots, int $slotCapacity = self::SLOT_CAPACITY): int
    {
        $blockedSlots = collect($blockedSlots);

        if ($blockedSlots->isEmpty()) {
            return 0;
        }

        $cursor = $proposedStart->copy()->seconds(0);
        $maxBlockedCapacity = 0;

        while ($cursor < $proposedEnd) {
            $sliceEnd = $cursor->copy()->addMinutes(30);
            $chairIds = [];
            $hasGlobalBlock = false;

            foreach ($blockedSlots as $blockedSlot) {
                $blockedStart = Carbon::parse($cursor->toDateString().' '.(string) $blockedSlot->start_time)->seconds(0);
                $blockedEnd = Carbon::parse($cursor->toDateString().' '.(string) $blockedSlot->end_time)->seconds(0);

                if (! ($blockedStart->lt($sliceEnd) && $blockedEnd->gt($cursor))) {
                    continue;
                }

                $chairId = isset($blockedSlot->chair_id) ? (int) $blockedSlot->chair_id : null;

                if (! in_array($chairId, [1, 2], true)) {
                    $hasGlobalBlock = true;
                    break;
                }

                $chairIds[$chairId] = true;
            }

            $sliceBlockedCapacity = $hasGlobalBlock ? $slotCapacity : count($chairIds);
            $maxBlockedCapacity = max($maxBlockedCapacity, $sliceBlockedCapacity);

            if ($maxBlockedCapacity >= $slotCapacity) {
                return $slotCapacity;
            }

            $cursor->addMinutes(30);
        }

        return $maxBlockedCapacity;
    }

    protected function resolveBlockingActor(): ?string
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        $firstName = trim((string) ($user->first_name ?? ''));
        $lastName = trim((string) ($user->last_name ?? ''));
        $fullName = trim($firstName.' '.$lastName);

        if ($fullName !== '') {
            return $fullName;
        }

        $username = trim((string) ($user->username ?? ''));
        if ($username !== '') {
            return $username;
        }

        return 'User #'.$user->id;
    }
}
