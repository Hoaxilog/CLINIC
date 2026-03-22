$base = 'c:\Users\admin\OneDrive\Documents\CLINIC_ONLINE\CLINIC'

function Patch($file, $replacements) {
    if (-not (Test-Path $file)) { Write-Host "SKIP (not found): $file"; return }
    $c = Get-Content $file -Raw -Encoding UTF8
    foreach ($r in $replacements) { $c = $c.Replace($r[0], $r[1]) }
    Set-Content $file $c -Encoding UTF8 -NoNewline
    Write-Host "PATCHED: $file"
}

# ===== routes/web.php =====
Patch "$base\routes\web.php" @(
    @('use App\Livewire\appointment\BookAppointment;', 'use App\Livewire\Appointment\BookAppointment;')
)

# ===== AppServiceProvider.php =====
Patch "$base\app\Providers\AppServiceProvider.php" @(
    @('use App\Livewire\appointment\AppointmentCalendar;', 'use App\Livewire\Appointment\AppointmentCalendar;'),
    @('use App\Livewire\appointment\AppointmentRequests;', '')
)

# ===== resources/views/appointment.blade.php =====
Patch "$base\resources\views\appointment.blade.php" @(
    @('\App\Livewire\appointment\AppointmentCalendar', '\App\Livewire\Appointment\AppointmentCalendar')
)

# ===== resources/views/appointment-requests.blade.php =====
Patch "$base\resources\views\appointment-requests.blade.php" @(
    @('\App\Livewire\appointment\AppointmentRequests', '\App\Livewire\Appointment\AppointmentRequests')
)

# ===== resources/views/dashboard.blade.php =====
Patch "$base\resources\views\dashboard.blade.php" @(
    @("@livewire('pending-approvals-widget')", "@livewire('dashboard.pending-approvals-widget')"),
    @("@livewire('cancelled-appointments-widget')", "@livewire('dashboard.cancelled-appointments-widget')")
)

# ===== resources/views/patient.blade.php =====
Patch "$base\resources\views\patient.blade.php" @(
    @("@livewire('patient-records')", "@livewire('patient.patient-records')"),
    @("@livewire('appointment-history-modal')", "@livewire('appointment.appointment-history-modal')"),
    @("@livewire('PatientFormController.patient-form-modal')", "@livewire('patient.form.patient-form-modal')")
)

# ===== resources/views/queue.blade.php =====
Patch "$base\resources\views\queue.blade.php" @(
    @("@livewire('today-schedule')", "@livewire('dashboard.today-schedule')")
)

# ===== resources/views/index.blade.php =====
Patch "$base\resources\views\index.blade.php" @(
    @("@livewire('components.notification-bell')", "@livewire('shared.notification-bell')")
)

# ===== resources/views/components/homepage/header-section.blade.php =====
Patch "$base\resources\views\components\homepage\header-section.blade.php" @(
    @("@livewire('components.notification-bell')", "@livewire('shared.notification-bell')")
)

# ===== Tests =====
Patch "$base\tests\Feature\GuestBookingAccessTest.php" @(
    @('use App\Livewire\appointment\BookAppointment;', 'use App\Livewire\Appointment\BookAppointment;')
)
Patch "$base\tests\Feature\BookAppointmentLegacySchemaTest.php" @(
    @('use App\Livewire\appointment\BookAppointment;', 'use App\Livewire\Appointment\BookAppointment;')
)
Patch "$base\tests\Feature\AppointmentCalendarPatientLinkingTest.php" @(
    @('use App\Livewire\appointment\AppointmentCalendar;', 'use App\Livewire\Appointment\AppointmentCalendar;')
)
Patch "$base\tests\Feature\PendingApprovalsWidgetTest.php" @(
    @('use App\Livewire\PendingApprovalsWidget;', 'use App\Livewire\Dashboard\PendingApprovalsWidget;')
)
Patch "$base\tests\Feature\PatientRecordsLayoutTest.php" @(
    @('use App\Livewire\PatientRecords;', 'use App\Livewire\Patient\PatientRecords;')
)
Patch "$base\tests\Feature\CancelledAppointmentsWidgetTest.php" @(
    @('use App\Livewire\CancelledAppointmentsWidget;', 'use App\Livewire\Dashboard\CancelledAppointmentsWidget;')
)
Patch "$base\tests\Feature\PatientFormBasicInfoTest.php" @(
    @('use App\Livewire\PatientFormController\BasicInfo;', 'use App\Livewire\Patient\Form\BasicInfo;')
)

Write-Host "`n=== All references updated ==="
