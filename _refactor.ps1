$base = 'c:\Users\admin\OneDrive\Documents\CLINIC_ONLINE\CLINIC'
$lw = "$base\app\Livewire"
$vw = "$base\resources\views\livewire"

# Ensure dirs exist
foreach ($d in @(
    "$lw\Appointment",
    "$lw\Dashboard",
    "$lw\Patient\Form",
    "$lw\Shared",
    "$vw\appointment",
    "$vw\dashboard",
    "$vw\patient\form\partial",
    "$vw\shared"
)) { New-Item -ItemType Directory -Force -Path $d | Out-Null }

# ---- Function to move+patch ----
function MovePHP($src, $dst, $replacements) {
    $c = Get-Content $src -Raw -Encoding UTF8
    foreach ($r in $replacements) { $c = $c.Replace($r[0], $r[1]) }
    Set-Content $dst $c -Encoding UTF8 -NoNewline
    Remove-Item $src -Force
    Write-Host "MOVED: $src -> $dst"
}

function MoveBlade($src, $dst, $replacements = @()) {
    $c = Get-Content $src -Raw -Encoding UTF8
    foreach ($r in $replacements) { $c = $c.Replace($r[0], $r[1]) }
    Set-Content $dst $c -Encoding UTF8 -NoNewline
    Remove-Item $src -Force
    Write-Host "MOVED BLADE: $dst"
}

# ===== PHASE 2: appointment/ -> Appointment/ =====

MovePHP "$lw\appointment\AppointmentCalendar.php" "$lw\Appointment\AppointmentCalendar.php" @(
    @('namespace App\Livewire\appointment;', 'namespace App\Livewire\Appointment;'),
    @("return view('livewire.appointment.appointment-calendar');", "return view('livewire.appointment.appointment-calendar');")
)

MovePHP "$lw\appointment\BookAppointment.php" "$lw\Appointment\BookAppointment.php" @(
    @('namespace App\Livewire\appointment;', 'namespace App\Livewire\Appointment;')
)

# ===== AppointmentHistoryModal: root -> Appointment/ =====
MovePHP "$lw\AppointmentHistoryModal.php" "$lw\Appointment\AppointmentHistoryModal.php" @(
    @('namespace App\Livewire;', 'namespace App\Livewire\Appointment;'),
    @("return view('livewire.appointment-history-modal');", "return view('livewire.appointment.appointment-history-modal');")
)

# ===== PHASE 3: PatientFormController/ -> Patient/Form/ =====
$pfcFiles = @('BasicInfo', 'DentalChart', 'DentalChartGrid', 'HealthHistory', 'TreatmentRecord')
foreach ($f in $pfcFiles) {
    $viewName = ($f -creplace '([A-Z])', '-$1').ToLower().TrimStart('-')
    MovePHP "$lw\PatientFormController\$f.php" "$lw\Patient\Form\$f.php" @(
        @('namespace App\Livewire\PatientFormController;', 'namespace App\Livewire\Patient\Form;'),
        @("return view('livewire.PatientFormViews.$viewName');", "return view('livewire.patient.form.$viewName');"),
        @("->to('PatientFormController.", "->to('patient.form.")
    )
}

# PatientFormModal needs special handling (also dispatches to sub-components)
$c = Get-Content "$lw\PatientFormController\PatientFormModal.php" -Raw -Encoding UTF8
$c = $c.Replace('namespace App\Livewire\PatientFormController;', 'namespace App\Livewire\Patient\Form;')
$c = $c.Replace("return view('livewire.PatientFormViews.patient-form-modal');", "return view('livewire.patient.form.patient-form-modal');")
$c = $c.Replace("->to('PatientFormController.", "->to('patient.form.")
Set-Content "$lw\Patient\Form\PatientFormModal.php" $c -Encoding UTF8 -NoNewline
Remove-Item "$lw\PatientFormController\PatientFormModal.php" -Force
Write-Host "MOVED: PatientFormModal.php"

# PatientRecords: root -> Patient/
MovePHP "$lw\PatientRecords.php" "$lw\Patient\PatientRecords.php" @(
    @('namespace App\Livewire;', 'namespace App\Livewire\Patient;'),
    @("return view('livewire.patient-records'", "return view('livewire.patient.patient-records'")
)

# ===== PHASE 4: Root loose files -> Dashboard/ =====
MovePHP "$lw\TodaySchedule.php" "$lw\Dashboard\TodaySchedule.php" @(
    @('namespace App\Livewire;', 'namespace App\Livewire\Dashboard;'),
    @("return view('livewire.today-schedule');", "return view('livewire.dashboard.today-schedule');")
)

MovePHP "$lw\PendingApprovalsWidget.php" "$lw\Dashboard\PendingApprovalsWidget.php" @(
    @('namespace App\Livewire;', 'namespace App\Livewire\Dashboard;'),
    @("return view('livewire.pending-approvals-widget');", "return view('livewire.dashboard.pending-approvals-widget');")
)

MovePHP "$lw\CancelledAppointmentsWidget.php" "$lw\Dashboard\CancelledAppointmentsWidget.php" @(
    @('namespace App\Livewire;', 'namespace App\Livewire\Dashboard;'),
    @("return view('livewire.cancelled-appointments-widget');", "return view('livewire.dashboard.cancelled-appointments-widget');")
)

# Notes.php
$c = Get-Content "$lw\Notes.php" -Raw -Encoding UTF8
$c = $c.Replace('namespace App\Livewire;', 'namespace App\Livewire\Dashboard;')
$c = $c.Replace("return view('livewire.notes'", "return view('livewire.dashboard.notes'")
Set-Content "$lw\Dashboard\Notes.php" $c -Encoding UTF8 -NoNewline
Remove-Item "$lw\Notes.php" -Force
Write-Host "MOVED: Notes.php"

# ===== PHASE 5: Components/ -> Shared/ =====
MovePHP "$lw\Components\NotificationBell.php" "$lw\Shared\NotificationBell.php" @(
    @('namespace App\Livewire\Components;', 'namespace App\Livewire\Shared;')
)

# ===== PHASE 6: Blade Views =====

# appointment-history-modal: root -> appointment/
MoveBlade "$vw\appointment-history-modal.blade.php" "$vw\appointment\appointment-history-modal.blade.php"

# PatientFormViews/ -> patient/form/
$pfvFiles = @('basic-info', 'dental-chart', 'dental-chart-grid', 'health-history', 'patient-form-modal', 'treatment-record')
foreach ($f in $pfvFiles) {
    # patch internal livewire: tags from PatientFormController.x to patient.form.x
    $c = Get-Content "$vw\PatientFormViews\$f.blade.php" -Raw -Encoding UTF8
    $c = $c.Replace('livewire:PatientFormController.', 'livewire:patient.form.')
    $c = $c.Replace("livewire('PatientFormController.", "livewire('patient.form.")
    $c = $c.Replace("@livewire('PatientFormController.", "@livewire('patient.form.")
    Set-Content "$vw\patient\form\$f.blade.php" $c -Encoding UTF8 -NoNewline
    Remove-Item "$vw\PatientFormViews\$f.blade.php" -Force
    Write-Host "MOVED BLADE: patient/form/$f"
}

# Move partial/tooth
MoveBlade "$vw\PatientFormViews\partial\tooth.blade.php" "$vw\patient\form\partial\tooth.blade.php"

# Root loose blades -> dashboard/
foreach ($f in @('today-schedule', 'pending-approvals-widget', 'cancelled-appointments-widget', 'notes')) {
    MoveBlade "$vw\$f.blade.php" "$vw\dashboard\$f.blade.php"
}

# patient-records -> patient/
MoveBlade "$vw\patient-records.blade.php" "$vw\patient\patient-records.blade.php"

# components/notification-bell -> shared/
MoveBlade "$vw\components\notification-bell.blade.php" "$vw\shared\notification-bell.blade.php"

Write-Host "`n=== All PHP + Blade files moved ==="
