$file = 'resources\views\livewire\appointment\partials\appointment-modal.blade.php'
$lines = [System.IO.File]::ReadAllLines($file)
Write-Host "Total lines: $($lines.Count)"

# Find and show the broken block
$startIdx = -1
$endIdx = -1
for ($i = 0; $i -lt $lines.Count; $i++) {
    if ($lines[$i] -match 'wire:key keeps morphdom') { $startIdx = $i; Write-Host "Found start at line $($i+1)" }
    if ($startIdx -ge 0 -and $lines[$i] -match 'overflow-hidden rounded-2xl border border-\[#cfe2f1\]' -and $i -gt $startIdx) { $endIdx = $i; Write-Host "Found end at line $($i+1)"; break }
}

if ($startIdx -lt 0 -or $endIdx -lt 0) { Write-Host "Could not find block"; exit 1 }

# Build replacement lines
$replacement = @(
    '                                {{-- wire:key preserves Alpine state across morphdom patches --}}',
    '                                <div x-data="{ selectedMatch: {{ $initialMatchId }} }"',
    '                                    wire:key="match-candidates-panel"',
    '                                    x-init="$watch(''selectedMatch'', v => $wire.selectedPendingPatientId = v)"',
    '                                    class="overflow-hidden rounded-2xl border border-[#cfe2f1] bg-white/90">'
)

$newLines = $lines[0..($startIdx-1)] + $replacement + $lines[($endIdx+1)..($lines.Count-1)]
[System.IO.File]::WriteAllLines($file, $newLines, [System.Text.UTF8Encoding]::new($false))
Write-Host "Done. New line count: $($newLines.Count)"
