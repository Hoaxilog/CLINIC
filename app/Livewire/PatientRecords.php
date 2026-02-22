<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;
use App\Models\Patient;


class PatientRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $sortOption = 'recent';
    public $selectedPatient;
    public $lastVisit;
    public $viewMode = 'table';
    public $linkEmailModalOpen = false;
    public $linkEmailPatientId = null;
    public $linkEmailPatientLabel = '';
    public $linkEmailOldEmail = '';
    public $newLinkedEmail = '';
    public $linkAccountIdentifier = '';
    public $confirmInPerson = false;
    public $confirmRecordMatch = false;
    protected $usesPatientUserId = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->fetchFirstPatient();
    }

    public function selectPatient($patientId)
    {
        $selectedPatient = null;

        if (Auth::check() && Auth::user()->role === 3) {
            $user = Auth::user();

            $query = DB::table('patients')->where('id', $patientId);
            if ($this->patientsUsesUserId()) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if (!empty($user->email)) {
                        $q->orWhere(function ($sub) use ($user) {
                            $sub->whereNull('user_id')
                                ->where('email_address', $user->email);
                        });
                    }
                });
            } else {
                $query->where('email_address', $user?->email);
            }

            $selectedPatient = $query->first();
            if (!$selectedPatient) {
                return;
            }

            // One-time backfill for legacy rows that still rely on email linkage.
            if ($this->patientsUsesUserId() && empty($selectedPatient->user_id)) {
                DB::table('patients')
                    ->where('id', $selectedPatient->id)
                    ->update([
                        'user_id' => $user->id,
                        'updated_at' => now(),
                    ]);
                $selectedPatient->user_id = $user->id;
            }

            $patientId = $selectedPatient->id;
        }

        $this->selectedPatient = $selectedPatient ?: DB::table('patients')->where('id', $patientId)->first();

        $this->lastVisit = DB::table('appointments')
                            ->where('patient_id', $patientId)
                            ->where('status', 'Completed')
                            ->orderBy('appointment_date', 'desc')
                            ->first();
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->fetchFirstPatient(); 
    }
    
    public function setSort($option)
    {
        $this->sortOption = $option;
        $this->resetPage();
        $this->fetchFirstPatient(); 
        $this->dispatch('closeSortDropdown');

    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    protected function getPatientsQuery()
    {
        $query = DB::table('patients');

        if (Auth::check() && Auth::user()->role === 3) {
            $user = Auth::user();

            if ($this->patientsUsesUserId()) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);

                    if (!empty($user->email)) {
                        $q->orWhere(function ($sub) use ($user) {
                            $sub->whereNull('user_id')
                                ->where('email_address', $user->email);
                        });
                    }
                });
            } else {
                $query->where('email_address', $user?->email);
            }

            return $query;
        }

        if (!empty($this->search)) {
            $query->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $this->search . '%');;
        }

        switch ($this->sortOption) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'a_z':
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                break;
            case 'z_a':
                $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    protected function fetchFirstPatient()
    {
        $patient = $this->getPatientsQuery()->first();
        
        if ($patient) {
            $this->selectPatient($patient->id);
        } else {
            $this->selectedPatient = null;
            $this->lastVisit = null;
        }
    }

    public function deletePatient($id)
    {
        if (Auth::check() && Auth::user()->role === 3) {
            session()->flash('error', 'You do not have permission to delete patient records.');
            return;
        }

        $patient = DB::table('patients')->where('id', $id)->first();

        if ($patient) {
            $patientSubject = new Patient();
            $patientSubject->id = $id;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($patientSubject)
                ->event('patient_deleted')
                ->withProperties([
                    'old' => (array) $patient,
                    'attributes' => [
                        'first_name' => $patient->first_name ?? null,
                        'last_name' => $patient->last_name ?? null,
                        'middle_name' => $patient->middle_name ?? null,
                        'mobile_number' => $patient->mobile_number ?? null,
                    ],
                ])
                ->log('Deleted Patient');

            DB::table('patients')->where('id', $id)->delete();

            // Reset selection if the deleted patient was currently being viewed
            if ($this->selectedPatient && $this->selectedPatient->id == $id) {
                $this->selectedPatient = null;
                $this->lastVisit = null;
            }
            session()->flash('error', 'Patient deleted successfully.');
        }
    }

    public function openLinkEmailModal($patientId)
    {
        if (Auth::check() && Auth::user()->role === 3) {
            session()->flash('error', 'You do not have permission to link patient email.');
            return;
        }

        $patient = DB::table('patients')->where('id', $patientId)->first();
        if (!$patient) {
            session()->flash('error', 'Patient not found.');
            return;
        }

        $this->linkEmailPatientId = $patient->id;
        $this->linkEmailPatientLabel = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
        $this->linkEmailOldEmail = $patient->email_address ?? '';
        $this->newLinkedEmail = $patient->email_address ?? '';
        $this->linkAccountIdentifier = $patient->email_address ?? '';
        $this->confirmInPerson = false;
        $this->confirmRecordMatch = false;
        $this->linkEmailModalOpen = true;
    }

    public function closeLinkEmailModal()
    {
        $this->linkEmailModalOpen = false;
    }

    public function linkPatientEmail()
    {
        if (Auth::check() && Auth::user()->role === 3) {
            session()->flash('error', 'You do not have permission to link patient email.');
            return;
        }

        $this->validate([
            'linkEmailPatientId' => ['required', 'integer'],
            'newLinkedEmail' => ['required', 'email', 'max:255'],
            'confirmInPerson' => ['accepted'],
            'confirmRecordMatch' => ['accepted'],
            'linkAccountIdentifier' => ['nullable', 'string', 'max:255'],
        ], [
            'confirmInPerson.accepted' => 'In-person identity check is required.',
            'confirmRecordMatch.accepted' => 'Patient record verification is required.',
        ]);

        $patient = DB::table('patients')->where('id', $this->linkEmailPatientId)->first();
        if (!$patient) {
            session()->flash('error', 'Patient not found.');
            return;
        }

        $usesUserId = $this->patientsUsesUserId();
        $linkedUserId = $usesUserId ? data_get($patient, 'user_id') : null;
        $newEmail = Str::lower(trim($this->newLinkedEmail));
        $identifier = trim((string) $this->linkAccountIdentifier);
        $identifier = $identifier === '' ? null : Str::lower($identifier);
        $oldEmail = $patient->email_address ? Str::lower(trim($patient->email_address)) : null;

        $matchedUser = null;
        if ($identifier) {
            $matchedUser = DB::table('users')
                ->whereRaw('LOWER(username) = ?', [$identifier])
                ->orWhereRaw('LOWER(email) = ?', [$identifier])
                ->first();
        } elseif ($usesUserId && !empty($linkedUserId)) {
            $matchedUser = DB::table('users')->where('id', $linkedUserId)->first();
        } elseif ($oldEmail) {
            $matchedUser = DB::table('users')
                ->whereRaw('LOWER(email) = ?', [$oldEmail])
                ->orWhereRaw('LOWER(username) = ?', [$oldEmail])
                ->first();
        }

        $verificationToken = null;
        $result = DB::transaction(function () use ($patient, $newEmail, $matchedUser, $usesUserId, &$verificationToken) {
            if ($matchedUser) {
                $emailInUse = DB::table('users')
                    ->where('email', $newEmail)
                    ->where('id', '!=', $matchedUser->id)
                    ->exists();

                if ($emailInUse) {
                    return 'email_conflict';
                }
            }

            $patientUpdate = [
                'email_address' => $newEmail,
                'modified_by' => Auth::user()?->username ?? 'SYSTEM',
                'updated_at' => now(),
            ];

            if ($usesUserId && $matchedUser) {
                $patientUpdate['user_id'] = $matchedUser->id;
            }

            DB::table('patients')
                ->where('id', $patient->id)
                ->update($patientUpdate);

            if (!$matchedUser) {
                return 'patient_only';
            }

            $verificationToken = Str::random(64);

            DB::table('users')
                ->where('id', $matchedUser->id)
                ->update([
                    'email' => $newEmail,
                    'email_verified_at' => null,
                    'verification_token' => $verificationToken,
                    'updated_at' => now(),
                ]);

            return 'patient_and_user';
        });

        if ($result === 'email_conflict') {
            session()->flash('error', 'Cannot link new email because it is already used by another user account.');
            return;
        }

        if ($result === 'patient_and_user' && $verificationToken) {
            Mail::send('auth.emails.verify-email', [
                'token' => $verificationToken,
                'id' => $matchedUser->id,
                'name' => 'Patient',
            ], function ($message) use ($newEmail) {
                $message->to($newEmail);
                $message->subject('Verify Your New Email Address - Tejadent');
            });
        }

        $this->linkEmailModalOpen = false;
        $this->dispatch('close-patient-menus');

        if ($result === 'patient_only') {
            session()->flash('info', 'Patient email updated. No user account was matched, so no verification email was sent.');
            return;
        }

        session()->flash('success', 'Patient email linked successfully. Verification email sent to the new address.');
    }

    public function render()
    {
        $patients = $this->getPatientsQuery()->paginate(10);
        $recoveryMap = $this->buildPendingRecoveryMap($patients->getCollection());

        $patients->setCollection(
            $patients->getCollection()->map(function ($patient) use ($recoveryMap) {
                $emailKey = $patient->email_address ? Str::lower(trim($patient->email_address)) : null;
                $userKey = null;
                if ($this->patientsUsesUserId() && !empty(data_get($patient, 'user_id'))) {
                    $userKey = 'user:' . (int) data_get($patient, 'user_id');
                }

                $pending = null;
                if ($userKey && isset($recoveryMap[$userKey])) {
                    $pending = $recoveryMap[$userKey];
                } elseif ($emailKey && isset($recoveryMap[$emailKey])) {
                    $pending = $recoveryMap[$emailKey];
                }

                $patient->pending_recovery_request_id = $pending['id'] ?? null;
                $patient->pending_recovery_requested_at = $pending['created_at'] ?? null;
                return $patient;
            })
        );

        return view('livewire.patient-records', compact('patients'));
    }

    protected function buildPendingRecoveryMap($patientsCollection): array
    {
        $userIds = collect();
        if ($this->patientsUsesUserId()) {
            $userIds = collect($patientsCollection)
                ->map(fn ($patient) => data_get($patient, 'user_id'))
                ->filter()
                ->unique()
                ->values();
        }

        $emails = collect($patientsCollection)
            ->pluck('email_address')
            ->filter()
            ->map(fn ($email) => Str::lower(trim((string) $email)))
            ->unique()
            ->values();

        if ($emails->isEmpty() && $userIds->isEmpty()) {
            return [];
        }

        try {
            $requests = DB::table('account_recovery_requests as arr')
                ->leftJoin('users as target', 'arr.user_id', '=', 'target.id')
                ->select(
                    'arr.id',
                    'arr.user_id as linked_user_id',
                    'arr.lookup_identifier',
                    'arr.new_email',
                    'arr.created_at',
                    'target.email as current_email'
                )
                ->where('arr.status', 'pending')
                ->where(function ($q) use ($emails, $userIds) {
                    $hasPreviousCondition = false;

                    if ($userIds->isNotEmpty()) {
                        $q->whereIn('arr.user_id', $userIds->all());
                        $hasPreviousCondition = true;
                    }

                    if ($emails->isNotEmpty()) {
                        $emailMatcher = function ($sub) use ($emails) {
                            $sub->whereIn(DB::raw('LOWER(COALESCE(target.email, ""))'), $emails->all())
                                ->orWhereIn(DB::raw('LOWER(COALESCE(arr.new_email, ""))'), $emails->all())
                                ->orWhereIn(DB::raw('LOWER(COALESCE(arr.lookup_identifier, ""))'), $emails->all());
                        };

                        if ($hasPreviousCondition) {
                            $q->orWhere($emailMatcher);
                        } else {
                            $q->where($emailMatcher);
                        }
                    }
                })
                ->orderBy('arr.created_at', 'desc')
                ->get();
        } catch (Throwable $e) {
            return [];
        }

        $map = [];
        foreach ($requests as $req) {
            if (!empty($req->linked_user_id)) {
                $userKey = 'user:' . (int) $req->linked_user_id;
                if (!isset($map[$userKey])) {
                    $map[$userKey] = [
                        'id' => $req->id,
                        'created_at' => $req->created_at,
                    ];
                }
            }

            $keys = collect([
                $req->current_email ?? null,
                $req->new_email ?? null,
                $req->lookup_identifier ?? null,
            ])
                ->filter()
                ->map(fn ($v) => Str::lower(trim((string) $v)))
                ->values();

            foreach ($keys as $key) {
                if (!isset($map[$key])) {
                    $map[$key] = [
                        'id' => $req->id,
                        'created_at' => $req->created_at,
                    ];
                }
            }
        }

        return $map;
    }

    protected function patientsUsesUserId(): bool
    {
        if ($this->usesPatientUserId !== null) {
            return $this->usesPatientUserId;
        }

        try {
            $this->usesPatientUserId = Schema::hasColumn('patients', 'user_id');
        } catch (Throwable $e) {
            $this->usesPatientUserId = false;
        }

        return $this->usesPatientUserId;
    }
}
